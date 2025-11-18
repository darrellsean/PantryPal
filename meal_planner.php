<?php
require_once 'config.php';
require_login();

$user_id = $_SESSION['user_id'];
$week_start = date('Y-m-d', strtotime('monday this week'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Meal Planner | PantryPal</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="styles_meal_planner.css">
</head>
<body>
<div class="dashboard">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
        <div class="logo"><img src="pantrypalwhite.png" alt="PantryPal"></div>
        <nav class="nav">
            <a href="home.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
            <a href="analytics.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Analytics</span></a>
            <a href="meal_planner.php" class="nav-item active"><i class="fas fa-calendar-alt"></i><span>Meal Planner</span></a>
            <a href="view_notifications.php" class="nav-item"><i class="fas fa-bell"></i><span>Notifications</span></a>
            <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
        <div class="sidebar-footer">
            <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
            <a href="../login-register/logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <div class="topbar">
            <h2>📅 Plan Weekly Meals</h2>
            <div class="top-actions">
                <input id="plannerWeek" type="date" value="<?php echo $week_start; ?>" />
                <button id="savePlanBtn" class="btn-primary">Save Week Plan</button>
            </div>
        </div>

        <div class="content-area">
            <div class="planner-intro">
                <p>Plan meals for each day. Suggestions prioritize ingredients expiring soon.</p>
            </div>

            <div id="plannerContainer" class="planner-container"></div>

            <div class="suggestions-panel">
                <h3>Suggestions (Expiring Soon / Matches / Generic)</h3>
                <div id="suggestionsList" class="suggestions-list"></div>
            </div>
        </div>

        <footer>
            <p>© 2025 PantryPal | All rights reserved.</p>
        </footer>
    </main>
</div>

<!-- MODAL -->
<div class="modal hidden" id="mealModal">
    <div class="modal-content">
        <h3 id="mealModalTitle">Add / Edit Meal</h3>
        <div class="modal-body">
            <label>Meal name
                <input type="text" id="modalMealName" placeholder="e.g. Pasta with veggies">
            </label>

            <div class="modal-recipe-section">
                <label>Choose from suggested recipes
                    <select id="modalRecipeSelect">
                        <option value="">— select suggestion —</option>
                    </select>
                </label>
            </div>

            <div class="modal-actions">
                <button id="modalSaveMeal" class="btn-primary">Save Meal</button>
                <button id="modalCancel" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts_meal_planner.js"></script>
<script>
document.getElementById('toggle-btn').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('collapsed');
});
</script>
</body>
</html>
