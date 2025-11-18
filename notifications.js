document.addEventListener("DOMContentLoaded", () => {

  const list = document.getElementById("notificationList");
  const deleteAllBtn = document.getElementById("deleteAll");

  // -----------------------------
  //  MARK READ / UNREAD
  // -----------------------------
  list.addEventListener("click", (e) => {

    // Mark as read / unread button
    if (e.target.classList.contains("mark-read")) {

      const item = e.target.closest(".notification-item");
      const id = item.dataset.id;

      // Toggle UI
      item.classList.toggle("read");
      item.classList.toggle("unread");
      const isRead = item.classList.contains("read");
      e.target.textContent = isRead ? "Unread" : "Read";

      // Update DB
      fetch("notification_mark_read.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `id=${id}&read=${isRead ? 1 : 0}`
      });

      return; // prevent other click logic
    }

    // -----------------------------
    // DELETE SINGLE NOTIFICATION
    // -----------------------------
    if (e.target.classList.contains("delete-btn")) {

      const item = e.target.closest(".notification-item");
      const id = item.dataset.id;

      // Remove from UI
      item.remove();
      checkEmpty();

      // Delete in DB
      fetch("notification_delete.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `id=${id}`
      });

      return;
    }
  });


  // -----------------------------
  // DELETE ALL NOTIFICATIONS
  // -----------------------------
  deleteAllBtn.addEventListener("click", () => {

    if (!confirm("Are you sure you want to delete all notifications?")) return;

    // Clear UI
    list.innerHTML = "";
    checkEmpty();

    // DB request
    fetch("notification_delete_all.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"}
    });
  });


  // -----------------------------
  // EMPTY STATE HANDLING
  // -----------------------------
  function checkEmpty() {
    if (list.children.length === 0) {
      list.innerHTML = '<p class="no-notifications">No new notifications</p>';
    }
  }

});
