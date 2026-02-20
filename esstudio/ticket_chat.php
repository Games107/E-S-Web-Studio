<?php
require "config.php";

$ticket_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tickets WHERE id=?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

$stmt = $conn->prepare("SELECT tm.*, u.username 
                        FROM ticket_messages tm
                        JOIN users u ON tm.sender_id = u.id
                        WHERE ticket_id=? ORDER BY tm.created_at ASC");
$stmt->execute([$ticket_id]);
$messages = $stmt->fetchAll();
?>

<link rel="stylesheet" href="style.css">

<div class="admin-container">
<h2>Chat: <?= $ticket['subject'] ?></h2>

<div class="chat-box">
<?php foreach($messages as $m): ?>
    <div class="chat-message">
        <strong><?= $m['username'] ?>:</strong>
        <?= $m['message'] ?>
    </div>
<?php endforeach; ?>
</div>

<form method="POST" action="send_message.php">
    <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
    <input type="text" name="message" placeholder="Nachricht..." required>
    <button>Senden</button>
</form>
</div>