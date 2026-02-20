<?php
require "config.php";

if(isset($_POST['register']) && $_POST['csrf'] === $_SESSION['csrf_token']) {

    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);

    header("Location: login.php");
}
?>

<link rel="stylesheet" href="style.css">

<form method="POST">
    <h2>Registrieren</h2>
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
    <button name="register">Registrieren</button>
    <p><a href="login.php">Zum Login</a></p>
</form>