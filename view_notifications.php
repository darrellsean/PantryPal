<?php
require_once 'config.php';

// Demo notifications — you will later fetch from DB
$notifications = [
    [
        'id' => 1,
        'title' => 'Milk is expiring soon!',
        'type' => 'inventory',
        'time' => '2025-10-12 10:00',
        'read' => false,
        'link' => 'inventory.php?filter=expiring'
    ],
    [
        'id' => 2,
        'title' => 'Donation request accepted!',
        'type' => 'donation',
        'time' => '2025-10-11 15:30',
        'read' => true,
        'link' => 'inventory.php?filter=donation'
    ],
    [
        'id' => 3,
        'title' => 'Meal reminder: Ingredients needed for planned meal',
        'type' => 'meal',
        'time' => '2025-10-10 09:45',
        'read' => false,
        'link' => 'inventory.php?filter=meal'
    ],
    [
        'id' => 4,
        'title' => 'Privacy alert: New login detected',
        'type' => 'security',
        'time' => '2025-10-09 21:15',
        'read' => false,
        'link' => 'settings.php'
    ],
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

            <!-- CORRECTED LINK HERE -->
            <a href="view_notifications.php" class="nav-item active">
                <i class="fas fa-bell"></i><span>Notifications</span>
            </a>

            <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>

        <div class="sidebar-footer">
            <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">

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
                            <div class="notification-item <?= $note['read'] ? 'read' : 'unread' ?>" data-id="<?= $note['id'] ?>">

                                <div class="notif-text">
                                    <a href="<?= htmlspecialchars($note['link']) ?>" class="notif-link">
                                        <strong><?= htmlspecialchars($note['title']) ?></strong>
                                    </a>

                                    <span class="notif-type"><?= ucfirst($note['type']) ?></span>
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
    document.getElementById('toggle-btn').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>
<script src="notifications.js"></script>
</body>
</html>
