<?php
require "config.php";

$ticket_id = $_POST['ticket_id'];
$message = $_POST['message'];
$sender_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("INSERT INTO ticket_messages (ticket_id, sender_id, message)
                        VALUES (?, ?, ?)");
$stmt->execute([$ticket_id, $sender_id, $message]);

header("Location: ticket_chat.php?id=".$ticket_id);