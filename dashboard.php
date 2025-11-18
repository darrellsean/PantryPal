<?php
require_once 'config.php';
require_login();

$user_id = $_SESSION['user_id'];

// --- Fetch Stats ---
$inventory_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM food_item WHERE user_id = ?");
$inventory_stmt->bind_param("i", $user_id);
$inventory_stmt->execute();
$inventory_count = $inventory_stmt->get_result()->fetch_assoc()['count'] ?? 0;

$week_start = date('Y-m-d', strtotime('monday this week'));
$meal_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM meal_plans WHERE user_id = ? AND week_start = ?");
$meal_stmt->bind_param("is", $user_id, $week_start);
$meal_stmt->execute();
$meal_plan_count = $meal_stmt->get_result()->fetch_assoc()['count'] ?? 0;

$today = date('Y-m-d');
$expiring_date = date('Y-m-d', strtotime('+3 days'));
$expiring_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM food_item WHERE user_id = ? AND expiry_date BETWEEN ? AND ?");
$expiring_stmt->bind_param("iss", $user_id, $today, $expiring_date);
$expiring_stmt->execute();
$expiring_soon_count = $expiring_stmt->get_result()->fetch_assoc()['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | PantryPal</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="styles_dashboard.css">
</head>
<body>
  <div class="dashboard">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
      <div class="logo"><img src="pantrypalwhite.png" alt="PantryPal Logo"></div>
      <nav class="nav">
        <a href="dashboard.php" class="nav-item active"><i class="fas fa-home"></i><span>Dashboard</span></a>
        <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
        <a href="analytics.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>
        <a href="meal_planner.php" class="nav-item"><i class="fas fa-calendar-alt"></i><span>Meal Planner</span></a>
        <a href="recipes.php" class="nav-item"><i class="fas fa-utensils"></i><span>Recipes</span></a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>

      </nav>
      <div class="sidebar-footer">
        <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="topbar">
        <h2>🏠 Pantry Dashboard Overview</h2>
        <input type="text" class="search-box" placeholder="Search...">
      </div>

      <!-- WELCOME SECTION -->
      <section class="welcome-section">
        <h1>Welcome to PantryPal 🎉</h1>
        <p>Track, plan, and organize your meals efficiently — all in one place.</p>
      </section>

      <!-- STATS GRID -->
      <section class="stats-grid">
        <div class="stat-card total">
          <h3>Total Inventory</h3>
          <p><?php echo $inventory_count; ?></p>
        </div>
        <div class="stat-card planned">
          <h3>Meals This Week</h3>
          <p><?php echo $meal_plan_count; ?></p>
        </div>
        <div class="stat-card warning">
          <h3>Expiring Soon</h3>
          <p><?php echo $expiring_soon_count; ?></p>
        </div>
      </section>

      <!-- QUICK ACTIONS -->
      <section class="quick-actions">
        <div class="action-card" onclick="window.location.href='inventory.php'">
          <i class="fas fa-boxes action-icon"></i>
          <h3>Manage Inventory</h3>
          <p>Add, edit, and view your food items</p>
        </div>

        <div class="action-card" onclick="window.location.href='meal_planner.php'">
          <i class="fas fa-calendar-check action-icon"></i>
          <h3>Plan Meals</h3>
          <p>Create your weekly meal plan</p>
        </div>

        <div class="action-card" onclick="window.location.href='recipes.php'">
          <i class="fas fa-utensils action-icon"></i>
          <h3>Browse Recipes</h3>
          <p>Discover new dishes with your ingredients</p>
        </div>

        <div class="action-card" onclick="window.location.href='inventory.php?filter=expiring'">
          <i class="fas fa-clock action-icon"></i>
          <h3>Use Expiring Items</h3>
          <p>Plan meals using soon-to-expire ingredients</p>
        </div>
      </section>

      <!-- RECENT ACTIVITY -->
      <section class="recent-activity">
        <h2>📈 Recent Activity</h2>
        <div class="activity-card">
          <i class="fas fa-chart-line"></i>
          <p>No recent activity to display</p>
        </div>
      </section>

      <footer>
        <p>&copy; 2025 PantryPal | All rights reserved.</p>
      </footer>
    </main>
  </div>

  <script>
    // Sidebar toggle
    document.getElementById('toggle-btn').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });
  </script>
</body>
</html>
