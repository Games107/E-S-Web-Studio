<?php
require "config.php";

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$ticket_id = $_GET['id'] ?? null;

if(!$ticket_id) {
    header("Location: my_tickets.php");
    exit();
}

// Ticket holen
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if(!$ticket || ($ticket['user_id'] != $user['id'] && $user['role'] != 'admin')) {
    header("Location: dashboard.php");
    exit();
}

// Nachrichten holen
$stmt = $conn->prepare("SELECT m.message, m.sender_id, u.username FROM ticket_messages m
                        JOIN users u ON m.sender_id = u.id
                        WHERE ticket_id = ? ORDER BY m.created_at ASC");
$stmt->execute([$ticket_id]);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Ticket #<?= $ticket_id ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">

<div class="main-content">
    <h2>Ticket: <?= htmlspecialchars($ticket['subject']) ?></h2>

    <a class="btn" href="<?= $user['role']=='admin'?'admin_tickets.php':'my_tickets.php' ?>">Zurück</a>

    <div class="chat-box" style="margin-top:20px;">
        <?php foreach($messages as $m): ?>
            <div class="chat-message">
                <strong><?= htmlspecialchars($m['username']) ?>:</strong> <?= htmlspecialchars($m['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if($ticket['status'] != 'closed'): ?>
        <form action="ticket_send.php" method="POST">
            <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="message" placeholder="Nachricht schreiben..." required>
            <button>Senden</button>
        </form>
    <?php else: ?>
        <p>Dieses Ticket ist geschlossen.</p>
    <?php endif; ?>
</div>

</body>
</html>