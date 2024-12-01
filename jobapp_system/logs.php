<?php

require_once 'core/dbConfig.php';

$sql = "SELECT al.timestamp, u.username, al.action FROM action_logs al
        JOIN users u ON al.user_id = u.id ORDER BY al.timestamp DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="index.php">Back to Home</a>
    
    <h1>Action Logs</h1>
    <table border="1">
        <tr>
            <th>Timestamp</th>
            <th>Username</th>
            <th>Action</th>
        </tr>
        <?php if (!empty($logs)): ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No logs found.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
