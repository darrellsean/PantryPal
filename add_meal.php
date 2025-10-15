<?php
require_once('config.php');
require_login();

$meal_name = $_POST['meal_name'];
$meal_date = $_POST['meal_date'];
$meal_time = $_POST['meal_time'];
$notes = $_POST['notes'];
$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("INSERT INTO meals (user_id, meal_name, meal_date, meal_time, notes) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $meal_name, $meal_date, $meal_time, $notes);

if ($stmt->execute()) {
  echo "✅ Meal added successfully!";
} else {
  echo "❌ Error: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();
?>
