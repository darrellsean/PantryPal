<?php
require_once('config.php');
require_login(); // ensures a user_id exists
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Meal Planner | PantryPal</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="wrapper">
  <div class="container">
    <h2 class="form-title">🍽 Weekly Meal Planner</h2>

    <form id="mealForm">
      <input type="text" name="meal_name" placeholder="Meal Name" required>
      <input type="date" name="meal_date" required>
      <select name="meal_time" required>
        <option value="">Select Mealtime</option>
        <option value="Breakfast">Breakfast</option>
        <option value="Lunch">Lunch</option>
        <option value="Dinner">Dinner</option>
        <option value="Snacks">Snacks</option>
      </select>
      <textarea name="notes" placeholder="Notes or ingredients (optional)"></textarea>
      <button type="submit" class="btn">Add Meal</button>
    </form>

    <h3 style="margin-top: 25px;">Your Planned Meals</h3>
    <div id="mealList">Loading your meals...</div>
  </div>
</div>

<script src="js/mealplanner.js"></script>
</body>
</html>
