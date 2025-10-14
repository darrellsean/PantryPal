<?php
session_start();
include("../loginregister/connect.php");

if(!isset($_SESSION['email'])){
  header("Location: ../loginregister/login.php");
  exit();
}

$email = $_SESSION['email'];
$query = mysqli_query($conn, "SELECT firstName, lastName FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);
$fullName = $user['firstName'] . " " . $user['lastName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PantryPal - Settings</title>
  <link rel="stylesheet" href="settings.css"> <!-- now includes sidebar -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
  <div class="container">
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
      <div class="logo">
        <img src="../homepage/pantrypalwhite.png" alt="PantryPal Logo">
      </div>
      <nav class="nav">
        <a href="../homepage/home.php" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="../manage-inventory/index.php" class="nav-item"><i class="fas fa-dashboard"></i><span>Dashboard</span></a>
        
      </nav>
      <div class="sidebar-footer">
        <p>Hi, <?php echo $fullName; ?> 👋</p>
        <a href="../loginregister/logout.php" class="logout-btn">Logout</a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="settings-container">
        <h2>Privacy & Security</h2>
        <div class="setting-item">
          <label for="enable2fa">Enable Two-Factor Authentication (2FA)</label>
          <label class="switch">
            <input type="checkbox" id="enable2fa">
            <span class="slider round"></span>
          </label>
        </div>
      </div>
    </main>
  </div>

  <script>
  const toggleBtn = document.getElementById('toggle-btn');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
  });

  document.getElementById("enable2fa").addEventListener("change", function() {
    if (this.checked) {
      fetch("send_verification.php", { method: "POST" })
        .then(response => response.text())
        .then(data => alert(data));
    } else {
      alert("2FA disabled.");
    }
  });
  </script>
</body>
</html>
