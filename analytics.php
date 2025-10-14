<?php
require_once 'config.php';
require_login();

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// 1️⃣ Count items by category
$category_data = [];
$category_query = $mysqli->prepare("SELECT category, COUNT(*) as count FROM food_item WHERE user_id = ? GROUP BY category");
$category_query->bind_param("i", $user_id);
$category_query->execute();
$result = $category_query->get_result();
while ($row = $result->fetch_assoc()) {
  $category_data[$row['category']] = (int)$row['count'];
}

// 2️⃣ Items expiring soon
$today = date('Y-m-d');
$soon = date('Y-m-d', strtotime('+3 days'));
$expiring_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM food_item WHERE user_id = ? AND expiry_date BETWEEN ? AND ?");
$expiring_stmt->bind_param("iss", $user_id, $today, $soon);
$expiring_stmt->execute();
$expiring = $expiring_stmt->get_result()->fetch_assoc()['count'];

// 3️⃣ Total items
$total_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM food_item WHERE user_id = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total = $total_stmt->get_result()->fetch_assoc()['count'];

// 4️⃣ Expired items
$expired_stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM food_item WHERE user_id = ? AND expiry_date < ?");
$expired_stmt->bind_param("is", $user_id, $today);
$expired_stmt->execute();
$expired = $expired_stmt->get_result()->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PantryPal | Analytics</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles_analytics.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="dashboard">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
      <div class="logo"><img src="pantrypalwhite.png" alt="PantryPal Logo"></div>
      <nav class="nav">
        <a href="home.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
        <a href="inventory.php" class="nav-item"><i class="fas fa-box"></i><span>Inventory</span></a>
        <a href="analytics.php" class="nav-item active"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>
        <a href="view_notifications.php" class="nav-item"><i class="fas fa-bell"></i><span>Notifications</span></a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>

      </nav>
      <div class="sidebar-footer">
        <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</p>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
      <div class="topbar">
        <h2>📊 Pantry Analytics Dashboard</h2>
      </div>

      <div class="analytics-container">
        <div class="summary-cards">
          <div class="summary-card total">
            <h3>Total Items</h3>
            <p><?php echo $total; ?></p>
          </div>
          <div class="summary-card expiring">
            <h3>Expiring Soon</h3>
            <p><?php echo $expiring; ?></p>
          </div>
          <div class="summary-card expired">
            <h3>Expired Items</h3>
            <p><?php echo $expired; ?></p>
          </div>
        </div>

        <div class="charts-grid">
          <div class="chart-card">
            <h3>🧺 Inventory by Category</h3>
            <canvas id="categoryChart"></canvas>
          </div>

          <div class="chart-card">
            <h3>⏳ Expiry Overview</h3>
            <canvas id="expiryChart"></canvas>
          </div>
        </div>
      </div>

      <footer>
        <p>&copy; 2025 PantryPal | Analytics Dashboard</p>
      </footer>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
  <script>
    // Sidebar toggle
    document.getElementById('toggle-btn').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });

    // Category Chart
    const categoryData = <?php echo json_encode($category_data); ?>;
    const ctx1 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx1, {
      type: 'doughnut',
      data: {
        labels: Object.keys(categoryData),
        datasets: [{
          data: Object.values(categoryData),
          backgroundColor: ['#1e90ff','#22c55e','#eab308','#ef4444','#a855f7','#14b8a6','#f97316','#0ea5e9'],
          borderWidth: 1,
        }]
      },
      options: {
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });

    // Expiry Chart
    new Chart(document.getElementById('expiryChart'), {
      type: 'bar',
      data: {
        labels: ['Total', 'Expiring Soon', 'Expired'],
        datasets: [{
          label: 'Items',
          data: [<?php echo $total; ?>, <?php echo $expiring; ?>, <?php echo $expired; ?>],
          backgroundColor: ['#22c55e','#f59e0b','#ef4444']
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      }
    });
  </script>
</body>
</html>
