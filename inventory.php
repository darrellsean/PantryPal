<?php 
require_once 'config.php'; 
require_login(); 

// 🔵 Get notification filter from URL
$activeFilter = $_GET['filter'] ?? 'none';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PantryPal | Manage Inventory</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles_inventory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
  <div class="container">

    <!-- SIDEBAR (Same as home.php) -->
    <aside class="sidebar" id="sidebar">
      <!-- Toggle Button -->
      <button class="toggle-btn" id="toggle-btn">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Logo -->
      <div class="logo">
        <img src="pantrypalwhite.png" alt="PantryPal Logo">
      </div>

      <!-- Navigation -->
      <nav class="nav">
        <a href="home.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
        <a href="inventory.php" class="nav-item active"><i class="fas fa-box"></i><span>Inventory</span></a>
        <a href="analytics.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Analytics</span></a>
        <a href="view_notifications.php" class="nav-item"><i class="fas fa-bell"></i><span>Notifications</span></a>
        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
      </nav>

      <!-- Footer -->
      <div class="sidebar-footer">
        <p>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</p>
        <a href="../login-register/logout.php" class="logout-btn">Logout</a>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
      <header class="topbar">
        <input id="searchInput" class="search-box" placeholder="🔍 Search items...">
        <button id="addItemBtn" class="btn-primary">+ Add Item</button>
      </header>

      <!-- FILTERS -->
      <div class="filters">
        <!-- Category Filter -->
        <select id="filterCategory">
          <option value="All">All Categories</option>
          <option value="Vegetable">Vegetable</option>
          <option value="Fruit">Fruit</option>
          <option value="Dairy">Dairy</option>
          <option value="Meat">Meat</option>
          <option value="Frozen">Frozen</option>
          <option value="Bakery">Bakery</option>
          <option value="Pantry">Pantry</option>
          <option value="Snacks">Snacks</option>
          <option value="Drinks">Drinks</option>
          <option value="Other">Other</option>
        </select>

        <!-- Status Filter -->
        <select id="filterStatus">
          <option value="All">All Status</option>
          <option value="Available">Available</option>
          <option value="Donation">Flagged for Donation</option>
          <option value="Meal">Arranged for Meal</option>
          <option value="Used">Used</option>
        </select>
      </div>

      <main class="inventory-area" id="inventoryList">
        <div id="emptyState" class="empty-state">
          <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="Empty" />
          <p>Your pantry is empty. Start adding items 🍎</p>
          <button id="addItemBtnEmpty" class="btn-primary">+ Add Item</button>
        </div>
      </main>

      <footer>
        <p>© 2025 PantryPal | All Rights Reserved</p>
      </footer>
    </div>
  </div>

  <!-- ADD/EDIT MODAL -->
  <div class="modal hidden" id="itemModal">
    <div class="modal-content">
      <h2 id="modalTitle">➕ Add New Item</h2>
      <form id="itemForm" class="form-grid">
        <input type="hidden" name="item_id" id="item_id">
        <label>Item Name <input type="text" id="item_name" name="item_name" class="input-field" required></label>
        <label>Category
          <select id="category" name="category" class="input-field">
            <option value="Vegetable">🥬 Vegetable</option>
            <option value="Fruit">🍎 Fruit</option>
            <option value="Dairy">🥛 Dairy</option>
            <option value="Meat">🥩 Meat</option>
            <option value="Frozen">❄️ Frozen</option>
            <option value="Bakery">🍞 Bakery</option>
            <option value="Pantry">🥫 Pantry</option>
            <option value="Snacks">🍪 Snacks</option>
            <option value="Drinks">🥤 Drinks</option>
            <option value="Other">📦 Other</option>
          </select>
        </label>
        <label>Quantity <input type="text" id="quantity" name="quantity" class="input-field" placeholder="e.g. 2 packs"></label>
        <label>Expiry Date <input type="date" id="expiry_date" name="expiry_date" class="input-field"></label>
        <div class="modal-actions">
          <button type="submit" class="btn-primary">💾 Save</button>
          <button type="button" id="closeModal" class="btn-secondary">❌ Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- DELETE MODAL -->
  <div class="modal hidden" id="deleteModal">
    <div class="modal-content">
      <h3>⚠️ Delete Item</h3>
      <p>Are you sure you want to remove this item?</p>
      <div class="modal-actions">
        <button id="confirmDelete" class="btn-danger">Yes, Delete</button>
        <button id="cancelDelete" class="btn-secondary">Cancel</button>
      </div>
    </div>
  </div>

  <!-- TOAST -->
  <div id="toast" class="toast hidden"></div>

  <script src="scripts_inventory.js"></script>

  <script>
    // Sidebar Toggle Functionality
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
    });
  </script>
</body>
</html>

