<?php
require "config.php";

if($_POST['csrf'] !== $_SESSION['csrf_token']) exit();

$subject = $_POST['subject'];
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("INSERT INTO tickets (user_id, subject) VALUES (?, ?)");
$stmt->execute([$user_id, $subject]);

header("Location: my_tickets.php");