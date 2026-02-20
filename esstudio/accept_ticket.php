<?php
require "config.php";

$id = $_POST['id'];
$admin_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("UPDATE tickets SET status='in_progress', admin_id=? WHERE id=?");
$stmt->execute([$user['id'], $ticket_id]);

// Notification
$stmt = $conn->prepare("INSERT INTO ticket_notifications (ticket_id, user_id, type, sender_name) VALUES (?, ?, 'accepted', ?)");
$stmt->execute([$ticket_id, $ticket['user_id'], $user['username']]);

header("Location: ticket_chat.php?id=".$id);