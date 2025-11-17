$(document).ready(function() {
  // Load meals when page loads
  loadMeals();

  // Add meal form submission
  $("#mealForm").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "add_meal.php",
      data: $(this).serialize(),
      success: function(response) {
        alert(response);
        $("#mealForm")[0].reset();
        loadMeals();
      }
    });
  });

  // Function to fetch and show meals
  function loadMeals() {
    $.ajax({
      url: "fetch_meals.php",
      method: "GET",
      success: function(data) {
        $("#mealList").html(data);
      }
    });
  }
});
