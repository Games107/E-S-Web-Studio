<?php
require "config.php";

if($_POST['csrf'] !== $_SESSION['csrf_token']) exit();

$id = $_POST['id'];
$role = $_POST['role'];

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$role, $id]);

header("Location: admin.php");
?>