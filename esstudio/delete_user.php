<?php
require "config.php";

if($_POST['csrf'] !== $_SESSION['csrf_token']) exit();

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header("Location: admin.php");
?>