<?php
require "config.php";

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

$user = $_SESSION['user'];

// Alle offenen Tickets
$stmt = $conn->query("SELECT t.id, t.subject, t.status, u.username as user_name 
                      FROM tickets t 
                      JOIN users u ON t.user_id = u.id
                      ORDER BY t.created_at DESC");
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin Tickets</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">

<div class="main-content">
    <h2>Alle Tickets</h2>

    <a class="btn" href="dashboard.php">Zurück zum Dashboard</a>

    <?php if(count($tickets) == 0): ?>
        <p style="margin-top:20px;">Keine Tickets vorhanden.</p>
    <?php else: ?>
        <?php foreach($tickets as $t): ?>
            <div class="ticket-box">
                <strong>User:</strong> <?= htmlspecialchars($t['user_name']) ?><br>
                <strong>Betreff:</strong> <?= htmlspecialchars($t['subject']) ?><br>
                <strong>Status:</strong> <?= ucfirst($t['status']) ?><br>
                <?php if($t['status'] == 'open'): ?>
                    <form action="ticket_accept.php" method="POST" style="margin-top:10px;">
                        <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                        <button>Ticket annehmen</button>
                    </form>
                <?php endif; ?>
                <a class="btn" href="ticket_view.php?id=<?= $t['id'] ?>" style="margin-top:10px;">Chat ansehen</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>