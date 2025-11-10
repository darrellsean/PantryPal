<?php
require_once('config.php');
require_login();

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM meals WHERE user_id = ? ORDER BY meal_date DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "
    <div class='meal-card'>
      <h4>{$row['meal_name']} <span>({$row['meal_time']})</span></h4>
      <p><strong>Date:</strong> {$row['meal_date']}</p>
      <p>{$row['notes']}</p>
    </div>";
  }
} else {
  echo "<p>No meals planned yet.</p>";
}

$stmt->close();
$mysqli->close();
?>
