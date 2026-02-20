<?php
require "config.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

// Benutzerliste abrufen
$stmt = $conn->query("SELECT id, username, email, role FROM users");
$users = $stmt->fetchAll();

// News hinzufügen
$successMessage = "";
$errorMessage = "";
if(isset($_POST['add_news'])) {
    // CSRF prüfen
    if(!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        die("Ungültiger CSRF-Token!");
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if($title !== "" && $description !== "") {
        $stmt = $conn->prepare("INSERT INTO news (title, description, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$title, $description]);
        $successMessage = "News erfolgreich hinzugefügt!";
    } else {
        $errorMessage = "Bitte Titel und Beschreibung ausfüllen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Verwaltung</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">

<div class="admin-container">
    <h2>Admin Verwaltung</h2>

    <!-- Erfolg / Fehler Meldungen -->
    <?php if($successMessage): ?>
        <p style="color: #00ff99; text-align:center; margin-bottom:15px;"><?= $successMessage ?></p>
    <?php endif; ?>
    <?php if($errorMessage): ?>
        <p style="color: #ff4d6d; text-align:center; margin-bottom:15px;"><?= $errorMessage ?></p>
    <?php endif; ?>

    <!-- Benutzer Tabelle -->
    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Rolle</th>
            <th>Aktion</th>
        </tr>

        <?php foreach($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['username'] ?></td>
            <td><?= $u['email'] ?></td>
            <td>
                <form action="update_role.php" method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                    <select name="role">
                        <option value="user" <?= $u['role']=="user"?"selected":"" ?>>User</option>
                        <option value="admin" <?= $u['role']=="admin"?"selected":"" ?>>Admin</option>
                    </select>
                    <button type="submit">Speichern</button>
                </form>
            </td>
            <td>
                <form action="delete_user.php" method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="delete">Löschen</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- NEWS ERSTELLEN -->
    <h2>News erstellen</h2>
    <form action="" method="POST" class="news-form">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="title">Titel:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Beschreibung:</label>
        <textarea name="description" id="description" required></textarea>

        <button type="submit" name="add_news">News speichern</button>
    </form>

    <a class="btn" href="dashboard.php">Zurück</a>
</div>

</body>
</html>