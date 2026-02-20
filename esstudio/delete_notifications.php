<?php
session_start();
require 'db_connection.php'; // PDO Connection

if(!isset($_SESSION['user'])) {
    http_response_code(403);
    exit('Nicht autorisiert');
}

$userId = $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}