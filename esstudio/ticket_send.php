<?php
require "config.php";

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// CSRF prüfen
if(!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
    die("Ungültiger CSRF-Token!");
}

$ticket_id = $_POST['ticket_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if(!$ticket_id || $message === '') {
    header("Location: dashboard.php");
    exit();
}

// Ticket prüfen
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if(!$ticket) die("Ticket existiert nicht.");

// Zugriffsprüfung
if($user['role'] != 'admin' && $ticket['user_id'] != $user['id']) {
    die("Zugriff verweigert.");
}

// Nachricht speichern
$stmt = $conn->prepare("INSERT INTO ticket_messages (ticket_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$ticket_id, $user['id'], $message]);
if($user['role'] === 'admin') {
    // alte user_notification Tabelle optional entfernen
    $stmt = $conn->prepare("INSERT INTO ticket_notifications (ticket_id, user_id, type, sender_name) VALUES (?, ?, 'reply', ?)");
    $stmt->execute([$ticket_id, $ticket['user_id'], $user['username']]);
}
// Admin antwortet → Ticketstatus & Benachrichtigung setzen
if($user['role'] === 'admin') {
    if($ticket['status'] === 'open') {
        $stmt = $conn->prepare("UPDATE tickets SET status='in_progress', admin_id=?, user_notification=1 WHERE id=?");
        $stmt->execute([$user['id'], $ticket_id]);
    } else {
        // nur Glocke setzen
        $stmt = $conn->prepare("UPDATE tickets SET user_notification=1 WHERE id=?");
        $stmt->execute([$ticket_id]);
    }
}

// Zurück zum Ticket
header("Location: ticket_view.php?id=$ticket_id");
exit();
?>