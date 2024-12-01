<?php

require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
    $response = ["message" => "", "statusCode" => 400, "querySet" => [], "result" => false];

    $sql = "SELECT * FROM applicants WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$username])) {
        $userInfoArray = $stmt->fetch();
        
        if ($stmt->rowCount() > 0) {
            $response = array(
                "result" => true,
                "statusCode" => 200,
                "querySet" => $userInfoArray
            );
        } else {
            $response["message"] = "User doesn't exist in the database";
        }
    }
    
    return $response;
}

function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM applicants");
    $querySet = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return ['querySet' => $querySet];
}

function getUserByID($pdo, $id) {
	$sql = "SELECT * from applicants WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

function searchForAUser($pdo, $userId, $searchQuery) {
    $sql = "SELECT * FROM applicants WHERE first_name LIKE ? OR last_name LIKE ?";
    
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute(["%".$searchQuery."%", "%".$searchQuery."%"]);

    if ($executeQuery) {
        logActivity($pdo, $userId,'SEARCHED');
        return $stmt->fetchAll();
    } else {
        return []; 
    }
}

function insertNewUser($pdo, $username, $first_name, $last_name, $email, $phone, $address, $position) {
    $response = ["message" => "", "statusCode" => 400];

    $checkIfUserExists = checkIfUserExists($pdo, $username);
    
    if (!$checkIfUserExists['result']) {

        $sql = "INSERT INTO applicants (username, first_name, last_name, email, phone, address, position)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$username, $first_name, $last_name, $email, $phone, $address, $position]);
        
        if ($executeQuery) {
            $userId = $pdo->lastInsertId();

            logActivity($pdo, $userId, 'INSERTED');

            $response["message"] = "Applicant inserted successfully.";
            $response["statusCode"] = 200;
        } else {
            $response["message"] = "Failed to insert applicant.";
        }
    } else {
        $response["message"] = "User already exists!";
    }
    return $response;
}

function editUser($pdo, $username, $first_name, $last_name, $email, $phone, $address, $position, $id) {
    $response = ["message" => "", "statusCode" => 400];
    
    $sql = "UPDATE applicants
            SET 
                username = ?,
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                address = ?,
                position = ?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$username, $first_name, $last_name, $email, $phone, $address, $position, $id]);

    if ($executeQuery) {
        logActivity($pdo, $id, 'UPDATED');

        $response["message"] = "Applicant updated successfully.";
        $response["statusCode"] = 200;
    } else {
        $response["message"] = "Failed to update applicant.";
    }

    return $response;
}

function deleteUser($pdo, $id) {
    $response = ["message" => "", "statusCode" => 400];

    $getUserSQL = "SELECT username FROM applicants WHERE id = ?";
    $getUserStmt = $pdo->prepare($getUserSQL);
    $getUserStmt->execute([$id]);
    $applicant = $getUserStmt->fetch();
    
    if (!$applicant) {
        $response["message"] = "Applicant not found.";
        return $response;
    }

    $username = $applicant['username'];

    $sql = "DELETE FROM applicants WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$id]);
    
    if ($executeQuery) {
        logActivity($pdo, $id, 'DELETED');

        $response["message"] = "Applicant deleted successfully.";
        $response["statusCode"] = 200;
    } else {
        $response["message"] = "Failed to delete applicant.";
    }

    return $response;
}

function getAllApplicants($pdo) {
    $sql = "SELECT * FROM applicants";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function insertApplicant($pdo, $username, $first_name, $last_name, $email, $phone, $address, $position) {
    $sql = "INSERT INTO applicants (username, first_name, last_name, email, phone, address, position) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $first_name, $last_name, $email, $phone, $address, $position]);
}

function deleteApplicant($pdo, $id) {
    $sql = "DELETE FROM applicants WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

function searchApplicants($pdo, $query) {
    $sql = "SELECT * FROM applicants WHERE first_name LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$query%"]);
    logActivity($pdo, $id, 'SEARCHED');
    return $stmt->fetchAll();
}

function registerUser($pdo, $username, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $hashedPassword]);
}

function loginUser($pdo, $username, $password) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Start session and store user info
        session_start();
        $_SESSION['user'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        
        // Optionally log the login activity
        logActivity($pdo, $user['id'], 'LOGGED_IN');
        
        return $user; // User successfully logged in
    }

    return null; // Return null if login failed
}

function logActivity($pdo, $user_id, $action): bool {
    $sql = "INSERT INTO action_logs (user_id, action) VALUES (?, ?)"; 
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$user_id, $action]);

    return $executeQuery;
}

function getAllActivityLogs($pdo) {
    $sql = "SELECT * FROM action_logs";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        return $stmt->fetchAll();
    }
}

?>
