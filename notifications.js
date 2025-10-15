document.addEventListener("DOMContentLoaded", () => {
  const list = document.getElementById("notificationList");
  const deleteAllBtn = document.getElementById("deleteAll");

  // Mark as read/unread
  list.addEventListener("click", (e) => {
    if (e.target.classList.contains("mark-read")) {
      const item = e.target.closest(".notification-item");
      item.classList.toggle("read");
      item.classList.toggle("unread");
      e.target.textContent = item.classList.contains("read") ? "Unread" : "Read";
    }

    // Delete single
    if (e.target.classList.contains("delete-btn")) {
      e.target.closest(".notification-item").remove();
      checkEmpty();
    }
  });

  // Delete all
  deleteAllBtn.addEventListener("click", () => {
    if (confirm("Are you sure you want to delete all notifications?")) {
      list.innerHTML = "";
      checkEmpty();
    }
  });

  function checkEmpty() {
    if (list.children.length === 0) {
      list.innerHTML = '<p class="no-notifications">No new notifications</p>';
    }
  }
});
