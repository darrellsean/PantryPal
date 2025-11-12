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

// Calculate totals
$total_saved = $status_counts['used'] + $status_counts['for meal'] + $status_counts['available'];
$total_donations = $status_counts['for donation'];
$total_expired = $status_counts['expired'];
$total_combined = $total_saved + $total_donations + $total_expired;

// Avoid division by zero
$saved_percent = $total_combined > 0 ? round(($total_saved / $total_combined) * 100, 1) : 0;
$donated_percent = $total_combined > 0 ? round(($total_donations / $total_combined) * 100, 1) : 0;
$expired_percent = $total_combined > 0 ? round(($total_expired / $total_combined) * 100, 1) : 0;
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
/* Charts & Stats */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(350px,1fr));
  gap: 25px;
}
.chart-card, .stats-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  text-align: center;
}
.chart-card h3, .stats-card h3 {
  color: #1e293b;
  margin-bottom: 15px;
}
.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #3399ff;
}
.stat-sub {
  color: #666;
  font-size: 1rem;
}
/* Footer */
footer {
  text-align: center;
  margin-top: 40px;
  color: #555;
}
/* ===== IMPACT CHART (NEW) ===== */
.impact-chart {
  max-width: 620px;
  margin: 40px auto 0;
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
      <div class="stats-card">
        <h3>Total Food Saved from Waste</h3>
        <p class="stat-value"><?php echo $total_saved; ?> <span style="font-size:1rem;color:#666;">(<?php echo $saved_percent; ?>%)</span></p>
        <p class="stat-sub">Items saved from expiration or waste</p>
      </div>
      <div class="stats-card">
        <h3>Number of Donations Made</h3>
        <p class="stat-value"><?php echo $total_donations; ?> <span style="font-size:1rem;color:#666;">(<?php echo $donated_percent; ?>%)</span></p>
        <p class="stat-sub">Items donated to others</p>
      </div>
    </div>

    <!-- Updated Chart: Impact Overview -->
    <div class="chart-card impact-chart">
      <h3>Impact Overview: Saved vs Donated</h3>
      <canvas id="impactChart"></canvas>
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

// Chart.js: Updated Impact Overview Doughnut
const impactData = {
  labels: ['Saved', 'Donated', 'Expired/Wasted'],
  datasets:[{
    data:[<?php echo $total_saved; ?>, <?php echo $total_donations; ?>, <?php echo $total_expired; ?>],
    backgroundColor:['#22c55e','#3b82f6','#ef4444']
  }]
};

new Chart(document.getElementById('impactChart'), {
  type:'doughnut',
  data:impactData,
  options:{
    responsive:true,
    cutout:'70%',
    plugins:{
      legend:{position:'bottom'},
      tooltip:{
        callbacks:{
          label:function(context){
            const total = context.dataset.data.reduce((a,b)=>a+b,0);
            const value = context.parsed;
            const percentage = ((value / total) * 100).toFixed(1);
            return `${context.label}: ${value} (${percentage}%)`;
          }
        }
      }
    }
  }
});
</script>
</body>
</html>
