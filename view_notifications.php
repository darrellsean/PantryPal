<?php
require_once 'config.php';
require_login();
session_start();

$user_id = $_SESSION['user_id'];

// Fetch notifications from DB (new → old)
$stmt = $mysqli->prepare("
    SELECT notification_id, title, message, type, link, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications | PantryPal</title>
    <link rel="stylesheet" href="style_notifications.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>

        <div class="logo">
            <img src="pantrypalwhite.png" alt="PantryPal Logo">
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
            <a href="analytics.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>

            <!-- FIXED LINK -->
            <a href="view_notifications.php" class="nav-item active">
                <i class="fas fa-bell"></i><span>Notifications</span>
            </a>

            <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>

        <div class="sidebar-footer">
            <p>Hi, <?= htmlspecialchars($_SESSION['user_name'] ?? "User") ?> 👋</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- HEADER -->
        <div class="topbar">
            <h2>Notifications</h2>
        </div>

        <div class="wrapper">
            <div class="container">
                <h2 class="form-title">Notifications</h2>

                <?php if (empty($notifications)) : ?>
                    <p class="no-notifications">No new notifications</p>

                <?php else: ?>

                    <div class="notification-list" id="notificationList">
                        <?php foreach ($notifications as $note): ?>
                            <div class="notification-item <?= $note['is_read'] ? 'read' : 'unread' ?>"
                                 data-id="<?= $note['notification_id'] ?>">

                                <div class="notif-text">

                                    <!-- CLICKABLE NOTIFICATION -->
                                    <a href="<?= htmlspecialchars($note['link'] ?? '#') ?>" class="notif-link">
                                        <strong><?= htmlspecialchars($note['title']) ?></strong>
                                    </a>

                                    <!-- TYPE BADGE -->
                                    <span class="notif-type"><?= ucfirst($note['type']) ?></span>

                                    <!-- TIMESTAMP -->
                                    <span class="time"><?= htmlspecialchars($note['created_at']) ?></span>

                                    <!-- MESSAGE BODY -->
                                    <p><?= htmlspecialchars($note['message']) ?></p>
                                </div>

                                <div class="notif-actions">
                                    <button class="mark-read">
                                        <?= $note['is_read'] ? 'Unread' : 'Read' ?>
                                    </button>

                                    <button class="delete-btn">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="notif-footer">
                        <button id="deleteAll">Delete All</button>
                    </div>

                <?php endif; ?>

            </div>
        </div>

        <footer>
            <p>&copy; 2025 PantryPal. All rights reserved.</p>
        </footer>
    </div>
</div>

<script>
    // Sidebar toggle
    document.getElementById('toggle-btn').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

<script src="notifications.js"></script>

</body>
</html>
