<?php
require "config.php";

if(isset($_POST['login']) && $_POST['csrf'] === $_SESSION['csrf_token']) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Falsche Login Daten!";
    }
}
?>

<link rel="stylesheet" href="style.css">

<form method="POST">
    <h2>Login</h2>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
    <button name="login">Login</button>
    <p><a href="register.php">Registrieren</a></p>
</form>