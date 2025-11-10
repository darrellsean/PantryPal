<?php
require_once 'config.php';
require_login();

$user_id = $_SESSION['user_id'] ?? 0;

// ----------------- FOOD ITEM ANALYTICS -----------------
$total_items_query = $mysqli->query("SELECT COUNT(*) AS total_items FROM food_item WHERE user_id = $user_id");
$total_items = $total_items_query->fetch_assoc()['total_items'] ?? 0;

// Count items by status
$statuses = ['used', 'for donation', 'available', 'for meal', 'expired'];
$status_counts = [];

foreach ($statuses as $status) {
    $query = $mysqli->query("SELECT COUNT(*) AS count FROM food_item WHERE user_id = $user_id AND LOWER(status) = '$status'");
    $status_counts[$status] = $query->fetch_assoc()['count'] ?? 0;
}

// ----------------- MEAL INSIGHTS -----------------
$meal_type_query = $mysqli->query("
    SELECT meal_type, COUNT(*) AS count
    FROM meal_plans
    WHERE user_id = $user_id
    GROUP BY meal_type
");

$meal_types = [];
$meal_counts = [];
while ($row = $meal_type_query->fetch_assoc()) {
    $meal_types[] = $row['meal_type'];
    $meal_counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PantryPal | Analytics</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* ===== GLOBAL ===== */
body {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  background: url('blue kitchen.png') no-repeat center center fixed;
  background-size: cover;
  overflow-x: hidden;
}
.container {
  display: flex;
  min-height: 100vh;
}
/* ===== SIDEBAR ===== */
.sidebar {
  width: 250px;
  background: #1e293b;
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 15px;
  transition: width 0.3s;
  position: fixed;
  height: 100%;
}
.sidebar .logo img {
  width: 200px;
  height: 60px;
  margin-bottom: 40px;
  transition: all 0.3s;
}
.toggle-btn {
  background: none;
  border: none;
  color: #3399ff;
  font-size: 1.5rem;
  cursor: pointer;
  margin-bottom: 20px;
}
.nav {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #cbd5e1;
  text-decoration: none;
  padding: 10px;
  border-radius: 8px;
  transition: all 0.3s;
}
.nav-item:hover, .nav-item.active {
  background: #3399ff;
  color: #fff;
}
.sidebar-footer {
  margin-top: auto;
  text-align: center;
  font-size: 14px;
}
/* ===== COLLAPSED SIDEBAR ===== */
.container.sidebar-collapsed .sidebar {
  width: 70px;
}
.container.sidebar-collapsed .sidebar .logo img {
  width: 50px;
  height: 40px;
}
.container.sidebar-collapsed .sidebar .nav-item span,
.container.sidebar-collapsed .sidebar .sidebar-footer {
  display: none;
}
.container.sidebar-collapsed .sidebar .nav-item {
  justify-content: center;
}
/* ===== MAIN CONTENT ===== */
.main-content {
  margin-left: 250px;
  flex: 1;
  padding: 25px;
  background: rgba(255,255,255,0.93);
  backdrop-filter: blur(10px);
  border-radius: 15px 0 0 15px;
  transition: margin-left 0.3s;
}
.container.sidebar-collapsed .main-content {
  margin-left: 70px;
}
/* Topbar */
.topbar {
  background: #1e293b;
  color: #fff;
  padding: 15px 25px;
  border-radius: 12px;
  margin-bottom: 25px;
  font-weight: 600;
}
/* Charts */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(350px,1fr));
  gap: 25px;
}
.chart-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.chart-card h3 {
  text-align: center;
  margin-bottom: 20px;
  color: #1e293b;
}
/* Footer */
footer {
  text-align: center;
  margin-top: 40px;
  color: #555;
}
</style>
</head>
<body>
<div class="container" id="container">
  <aside class="sidebar">
    <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
    <div class="logo"><img src="pantrypalwhite.png" alt="Logo"></div>
    <nav class="nav">
      <a href="home.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
      <a href="analytics.php" class="nav-item active"><i class="fas fa-chart-line"></i><span>Analytics</span></a>
      <a href="view_notifications.php" class="nav-item"><i class="fas fa-bell"></i><span>Notifications</span></a>
      <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
      <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
    </nav>
    <div class="sidebar-footer">
      <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
      <a href="../login-register/logout.php">Logout</a>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">Food Analytics Dashboard</header>

    <?php if ($total_items == 0): ?>
      <p style="text-align:center;color:#888;">No food-saving activities found. Start logging or donating items to view your progress!</p>
    <?php else: ?>
    <div class="charts-grid">
      <div class="chart-card">
        <h3>Food Status Breakdown</h3>
        <canvas id="foodPieChart"></canvas>
      </div>
      <?php if (!empty($meal_types)): ?>
      <div class="chart-card">
        <h3>Meals by Type</h3>
        <canvas id="mealBarChart"></canvas>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <footer>© 2025 PantryPal | All Rights Reserved</footer>
  </div>
</div>

<script>
// Sidebar toggle
document.getElementById('toggle-btn').addEventListener('click', function(){
  document.getElementById('container').classList.toggle('sidebar-collapsed');
});

// Chart.js: Food Status Pie
const foodData = {
  labels: ['Used','For Donation','Available','For Meal','Expired'],
  datasets:[{
    data:[
      <?php echo $status_counts['used']; ?>,
      <?php echo $status_counts['for donation']; ?>,
      <?php echo $status_counts['available']; ?>,
      <?php echo $status_counts['for meal']; ?>,
      <?php echo $status_counts['expired']; ?>
    ],
    backgroundColor:['#36A2EB','#FF6384','#22c55e','#f59e0b','#ef4444']
  }]
};
new Chart(document.getElementById('foodPieChart'), {type:'pie', data:foodData});

// Chart.js: Meal Types Bar
<?php if (!empty($meal_types)): ?>
const mealData = {
  labels: <?php echo json_encode($meal_types); ?>,
  datasets:[{
    label:'Number of Meals',
    data: <?php echo json_encode($meal_counts); ?>,
    backgroundColor:'#36A2EB'
  }]
};
new Chart(document.getElementById('mealBarChart'), {type:'bar', data:mealData, options:{scales:{y:{beginAtZero:true}}}});
<?php endif; ?>
</script>
</body>
</html>
