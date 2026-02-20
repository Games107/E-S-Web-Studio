<?php
session_start();
require "config.php";

if(!isset($_SESSION['user'])) {
    http_response_code(403);
    exit('Nicht autorisiert');
}

$userId = $_SESSION['user']['id'];

// Alle ungesehenen Benachrichtigungen als gelesen markieren
$stmt = $conn->prepare("UPDATE ticket_notifications SET seen = 1 WHERE user_id = ? AND seen = 0");
$stmt->execute([$userId]);

echo json_encode(['status' => 'success']);