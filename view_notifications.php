<?php
require_once 'config.php';

// Example demo notifications — later you can fetch from database
$notifications = [
    ['id' => 1, 'title' => 'Milk is expiring soon!', 'time' => '2025-10-12 10:00', 'read' => false],
    ['id' => 2, 'title' => 'Donation request accepted!', 'time' => '2025-10-11 15:30', 'read' => true],
    ['id' => 3, 'title' => 'Your new recipe got approved!', 'time' => '2025-10-10 09:45', 'read' => false],
];
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

    <!-- SIDEBAR (copied from home.php) -->
    <aside class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
        <div class="logo">
            <img src="pantrypalwhite.png" alt="PantryPal Logo">
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
            <a href="analytics.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>
            <a href="notifications.php" class="nav-item active"><i class="fas fa-bell"></i><span>Notifications</span></a>
            <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>

        </nav>

        <div class="sidebar-footer">
            <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-content">

        <!-- HEADER / TOPBAR -->
        <div class="topbar">
            <h2>Notifications</h2>
        </div>

        <!-- ORIGINAL NOTIFICATION CONTENT (unchanged) -->
        <div class="wrapper">
            <div class="container">
                <h2 class="form-title">Notifications</h2>

                <?php if (empty($notifications)) : ?>
                    <p class="no-notifications">No new notifications</p>
                <?php else: ?>
                    <div class="notification-list" id="notificationList">
                        <?php foreach ($notifications as $note): ?>
                            <div class="notification-item <?= $note['read'] ? 'read' : 'unread' ?>" data-id="<?= $note['id'] ?>">
                                <div class="notif-text">
                                    <strong><?= htmlspecialchars($note['title']) ?></strong>
                                    <span class="time"><?= htmlspecialchars($note['time']) ?></span>
                                </div>
                                <div class="notif-actions">
                                    <button class="mark-read"><?= $note['read'] ? 'Unread' : 'Read' ?></button>
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
