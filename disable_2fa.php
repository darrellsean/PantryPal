<?php
session_start();
include("../loginregister/connect.php");

if (!isset($_SESSION['email'])) {
  echo "User not logged in.";
  exit();
}

$email = $_SESSION['email'];
$conn->query("UPDATE users SET twofa_enabled = 0 WHERE email='$email'");
echo "Two-Factor Authentication has been disabled.";
?>
