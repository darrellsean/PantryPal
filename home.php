<?php
session_start();
include("../loginregister/connect.php");

if(!isset($_SESSION['email'])){
  header("Location: ../loginregister/login.php"); // redirect if not logged in
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
  <title>PantryPal - Home</title>
  <link rel="stylesheet" href="home.css">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <div class="container">
    
    <!-- Sidebar -->
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
    <a href="../manage-inventory/index.php" class="nav-item">
        <i class="fas fa-dashboard"></i><span>Dashboard</span>
    <a href="../settings/settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
  </nav>

  <!-- Greeting -->
  <div class="sidebar-footer">
    <p>Hi, <?php echo $fullName; ?> 👋</p>
    <a href="../loginregister/logout.php" class="logout-btn">Logout</a>

  </div>
</aside>


  <script>
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
    });
  </script>
</body>
</html>
