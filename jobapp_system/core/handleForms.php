<?php
require_once 'dbConfig.php';
require_once 'models.php';
  

if (isset($_POST['insertUserBtn'])) {
	$insertUser = insertNewUser($pdo,$_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['position']);

	if ($insertUser) {
		$_SESSION['message'] = "Successfully inserted!";
		header("Location: ../index.php");
	}
}

if (isset($_POST['editUserBtn'])) {
    $id = $_GET['id'];
    $editUser = editUser($pdo, $_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['position'], $id);
    if ($editUser) {
        $_SESSION['message'] = "Successfully edited!";
        header("Location: ../index.php");
    }
}


if (isset($_POST['deleteUserBtn'])) {
	$deleteUser = deleteUser($pdo,$_GET['id']);

	if ($deleteUser) {
		$_SESSION['message'] = "Successfully deleted!";
		header("Location: ../index.php");
	}
}

if (isset($_GET['searchBtn']) && isset($_GET['searchInput'])) {
    $searchInput = trim($_GET['searchInput']);
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $searchForAUser = searchForAUser($pdo, $userId, $searchInput);
    } else {

        $_SESSION['message'] = "You need to be logged in to search.";
        header("Location: login.php");
        exit;
    }
}

if (isset($_POST['registerBtn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (registerUser($pdo, $username, $password)) {
        $_SESSION['message'] = "Registration successful!";
        header("Location: login.php");
    } else {
        $_SESSION['message'] = "Registration failed!";
        header("Location: register.php");
    }
}

if (isset($_POST['loginBtn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = loginUser($pdo, $username, $password);
    if ($user) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['message'] = "Welcome, {$user['username']}!";
        header("Location: index.php");
    } else {
        $_SESSION['message'] = "Invalid login credentials!";
        header("Location: login.php");
    }
}

?>


