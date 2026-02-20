<?php
require "config.php";

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// News aus DB abrufen (neueste zuerst)
$stmtNews = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
$newsList = $stmtNews->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<script>
function showSection(section) {
    document.querySelectorAll(".content-section").forEach(el => el.style.display = "none");
    document.getElementById(section).style.display = "block";
}

// Demo Notification
function notify() {
    document.getElementById("bell").classList.add("active");
}
</script>
</head>
<body class="dashboard-body">

<!-- TOP BAR -->
<div class="topbar">
    <div class="top-right">
<?php
// neue Benachrichtigungen abrufen
$stmt = $conn->prepare("SELECT * FROM ticket_notifications WHERE user_id=? AND seen=0 ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$notifications = $stmt->fetchAll();
?>

<div class="menu-container" style="position: relative;">
    <div class="notification">🔔
        <?php if(count($notifications) > 0): ?>
            <span id="bell" class="bell-dot active"></span>
        <?php endif; ?>
    </div>
    
    <div class="menu-dropdown">
        <?php if(count($notifications) == 0): ?>
            <a>Keine neuen Benachrichtigungen</a>
        <?php else: ?>
            <?php foreach($notifications as $n): ?>
                <a href="ticket_view.php?id=<?= $n['ticket_id'] ?>">
                    <?php if($n['type'] == 'accepted'): ?>
                        <?= htmlspecialchars($n['sender_name']) ?> hat dein Ticket angenommen
                    <?php elseif($n['type'] == 'reply'): ?>
                        <?= htmlspecialchars($n['sender_name']) ?> hat in deinem Ticket geantwortet
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const container = document.querySelector('.menu-container');
const dropdown = document.querySelector('.menu-dropdown');
const bellDot = document.getElementById('bell');

container.addEventListener('mouseleave', function() {
    dropdown.innerHTML = '<a>Keine neuen Benachrichtigungen</a>';
    if(bellDot) {
        bellDot.style.opacity = 0;
        setTimeout(() => bellDot.style.display = 'none', 300);
    }
    fetch('delete_notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: <?= $user['id'] ?> })
    })
    .then(response => response.json())
    .then(data => { if(data.success){ console.log('Notifications gelöscht'); } })
    .catch(err => console.error(err));
});
</script>

        <div class="profile">
            <div class="profile-pic">👤</div>
            <div class="username"><?= $user['username']; ?></div>
        </div>

        <!-- DROPDOWN HOVER -->
        <div class="menu-container">
            <div class="menu">☰</div>
            <div class="menu-dropdown">
                <?php if($user['role'] === "admin"): ?>
                    <a href="admin_tickets.php">Anfragen</a>
                    <a href="admin.php">Verwaltung</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div onclick="showSection('willkommen')">①</div>
    <div onclick="showSection('eins')">②</div>
    <div onclick="showSection('zwei')">③</div>
    <div onclick="showSection('drei')">④</div>
    <div onclick="showSection('vier')">⑤</div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- WILLKOMMEN + NEWS -->
    <div id="willkommen" class="content-section">
        <h2>Willkommen <?= $user['username']; ?></h2>
        <p>Hier findest du alle aktuellen Funktionen und News.</p>

        <!-- NEWS SECTION -->
        <div class="news-section">
            <?php if(count($newsList) === 0): ?>
                <p>Keine News vorhanden.</p>
            <?php else: ?>
                <?php foreach($newsList as $news): ?>
                    <div class="news-item">
                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($news['description'])) ?></p>
                        <small>Erstellt am: <?= date("d.m.Y H:i", strtotime($news['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- TICKET ERSTELLEN -->
    <div id="eins" class="content-section" style="display:none;">
        <h2>Ticket System</h2>
        <form action="create_ticket.php" method="POST">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="subject" placeholder="Betreff" required>
            <button>Ticket erstellen</button>
        </form>
        <br>
        <a class="btn" href="my_tickets.php">Anfragen Übersicht</a>
    </div>

    <!-- DUMMY CONTENT -->
    <div id="zwei" class="content-section" style="display:none;">
        <h2>Zwei</h2>
        <p>Inhalt zwei</p>
    </div>

    <div id="drei" class="content-section" style="display:none;">
        <h2>Drei</h2>
        <p>Inhalt drei</p>
    </div>

    <div id="vier" class="content-section" style="display:none;">
        <h2>Vier</h2>
        <p>Inhalt vier</p>
    </div>

</div>

</body>
</html>