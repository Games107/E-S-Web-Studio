<?php
require "config.php";

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Tickets vom User holen
$stmt = $conn->prepare("SELECT t.id, t.subject, t.status, u.username as admin_name 
                        FROM tickets t 
                        LEFT JOIN users u ON t.admin_id = u.id
                        WHERE t.user_id = ? 
                        ORDER BY t.created_at DESC");
$stmt->execute([$user['id']]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Meine Tickets</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">

<div class="main-content">
    <h2>Meine Tickets</h2>

    <a class="btn" href="dashboard.php">Zurück zum Dashboard</a>

    <?php if(count($tickets) == 0): ?>
        <p style="margin-top:20px;">Du hast noch keine Tickets erstellt.</p>
    <?php else: ?>
        <?php foreach($tickets as $t): ?>
            <div class="ticket-box">
                <strong>Betreff:</strong> <?= htmlspecialchars($t['subject']) ?><br>
                <strong>Status:</strong> <?= ucfirst($t['status']) ?><br>
                <?php if($t['admin_name']): ?>
                    <strong>Bearbeitet von:</strong> <?= htmlspecialchars($t['admin_name']) ?>
                <?php endif; ?>
                <br>
                <?php if($t['status'] != 'closed'): ?>
                    <a class="btn" href="ticket_view.php?id=<?= $t['id'] ?>">Ansehen / Chat</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>