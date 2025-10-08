let selectedDeleteId = null;
let allItems = []; // store all fetched items

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("itemModal");
  const deleteModal = document.getElementById("deleteModal");
  const form = document.getElementById("itemForm");
  const inventoryList = document.getElementById("inventoryList");
  const toast = document.getElementById("toast");
  const emptyState = document.getElementById("emptyState");
  const searchInput = document.getElementById("searchInput");

  // 🌟 Toast Notification
  function showToast(msg, color = "#1976d2") {
    toast.textContent = msg;
    toast.style.background = color;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2500);
  }

  // 🧩 Category Icons
  function getCategoryIcon(cat) {
    const icons = {
      Vegetable: "🥬",
      Fruit: "🍎",
      Dairy: "🥛",
      Meat: "🥩",
      Frozen: "❄️",
      Bakery: "🍞",
      Pantry: "🥫",
      Snacks: "🍪",
      Drinks: "🥤",
      Other: "📦"
    };
    return icons[cat] || "📦";
  }

  // 🧾 Render all items
  function renderInventory(items) {
    inventoryList.innerHTML = "";

    if (!items || items.length === 0) {
      emptyState.classList.remove("hidden");
      inventoryList.appendChild(emptyState);
      return;
    }

    emptyState.classList.add("hidden");

    items.forEach(item => {
      const today = new Date();
      let cssClass = "fresh";
      if (item.expiry_date) {
        const exp = new Date(item.expiry_date);
        const diff = (exp - today) / (1000 * 60 * 60 * 24);
        if (diff < 0) cssClass = "expired";
        else if (diff <= 3) cssClass = "expiring";
      }

      inventoryList.innerHTML += `
        <div class="inventory-card ${cssClass}">
          <h3>${getCategoryIcon(item.category)} ${item.item_name}</h3>
          <div class="card-meta">Category: ${item.category || "N/A"}</div>
          <div class="card-meta">Qty: ${item.quantity || "N/A"}</div>
          <div class="card-meta">Expires: ${item.expiry_date || "—"}</div>
          <div class="card-actions">
            <button class="btn-secondary" onclick="editItem(${item.item_id})">✏️ Edit</button>
            <button class="btn-danger" onclick="showDelete(${item.item_id})">🗑️ Delete</button>
          </div>
        </div>`;
    });
  }

  // 📦 Fetch Inventory
  function fetchInventory() {
    fetch("api_inventory.php?action=list")
      .then(r => r.json())
      .then(data => {
        allItems = data.items || [];
        renderInventory(allItems);
      });
  }

  // 🔍 Search Functionality
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim().toLowerCase();
    if (query === "") {
      renderInventory(allItems);
      return;
    }

    const filtered = allItems.filter(item =>
      item.item_name.toLowerCase().includes(query) ||
      (item.category && item.category.toLowerCase().includes(query))
    );

    if (filtered.length > 0) {
      renderInventory(filtered);
    } else {
      // no items found message
      inventoryList.innerHTML = `
        <div class="not-found">
          <img src="https://cdn-icons-png.flaticon.com/512/2748/2748558.png" width="120">
          <p>😢 No items found for "<b>${query}</b>"</p>
        </div>`;
    }
  });

  // 🧰 Add Item
  document.getElementById("addItemBtn").onclick = () => {
    modal.classList.remove("hidden");
    form.reset();
    document.getElementById("modalTitle").textContent = "➕ Add New Item";
  };
  document.getElementById("addItemBtnEmpty").onclick = () => {
    modal.classList.remove("hidden");
    form.reset();
    document.getElementById("modalTitle").textContent = "➕ Add New Item";
  };

  document.getElementById("closeModal").onclick = () => modal.classList.add("hidden");

  // 💾 Save Item
  form.onsubmit = e => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch("api_inventory.php?action=save", { method: "POST", body: formData })
      .then(r => r.json())
      .then(() => {
        modal.classList.add("hidden");
        fetchInventory();
        showToast("✅ Item successfully saved!");
      });
  };

  // ✏️ Edit Item
  window.editItem = function (id) {
    fetch("api_inventory.php?action=get&id=" + id)
      .then(r => r.json())
      .then(data => {
        modal.classList.remove("hidden");
        document.getElementById("modalTitle").textContent = "✏️ Edit Item";
        document.getElementById("item_id").value = data.item.item_id;
        document.getElementById("item_name").value = data.item.item_name;
        document.getElementById("category").value = data.item.category;
        document.getElementById("quantity").value = data.item.quantity;
        document.getElementById("expiry_date").value = data.item.expiry_date;
      });
  };

  // 🗑️ Delete Item
  window.showDelete = function (id) {
    selectedDeleteId = id;
    deleteModal.classList.remove("hidden");
  };

  document.getElementById("cancelDelete").onclick = () => deleteModal.classList.add("hidden");

  document.getElementById("confirmDelete").onclick = () => {
    fetch("api_inventory.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + selectedDeleteId
    }).then(() => {
      deleteModal.classList.add("hidden");
      fetchInventory();
      showToast("🗑️ Item deleted!", "#e53935");
    });
  };

  // Initial Load
  fetchInventory();
});
