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
  const filterCategory = document.getElementById("filterCategory");
  const filterStatus = document.getElementById("filterStatus");

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
          <div class="card-meta">
            Status: 
            <select class="status-dropdown" data-id="${item.item_id}">
              <option value="Available" ${item.status==='Available'?'selected':''}>Available</option>
              <option value="Donation" ${item.status==='Donation'?'selected':''}>Flagged for Donation</option>
              <option value="Meal" ${item.status==='Meal'?'selected':''}>Arranged for Meal</option>
              <option value="Used" ${item.status==='Used'?'selected':''}>Used</option>
            </select>
          </div>
          <div class="card-actions">
            <button class="btn-secondary" onclick="editItem(${item.item_id})">✏️ Edit</button>
            <button class="btn-danger" onclick="showDelete(${item.item_id})">🗑️ Delete</button>
          </div>
        </div>`;
    });

    // Attach event listener for status change
    document.querySelectorAll(".status-dropdown").forEach(sel => {
      sel.addEventListener("change", e => {
        const id = e.target.dataset.id;
        const status = e.target.value;
        fetch("api_inventory.php?action=update_status", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}&status=${status}`
        }).then(() => showToast("✅ Status updated!", "#f59e0b"));
      });
    });
  }

  // 📦 Fetch Inventory
  function fetchInventory() {
    fetch("api_inventory.php?action=list")
      .then(r => r.json())
      .then(data => {
        allItems = data.items || [];
        applyFilters();
      });
  }

  // 🔍 Filter & Search
  function applyFilters() {
    const query = searchInput.value.trim().toLowerCase();
    const catFilter = filterCategory.value;
    const statusFilter = filterStatus.value;

    let filtered = allItems;

    if (query) {
      filtered = filtered.filter(item =>
        item.item_name.toLowerCase().includes(query) ||
        (item.category && item.category.toLowerCase().includes(query))
      );
    }

    if (catFilter !== "All") {
      filtered = filtered.filter(item => item.category === catFilter);
    }

    if (statusFilter !== "All") {
      filtered = filtered.filter(item => item.status === statusFilter);
    }

    renderInventory(filtered.length ? filtered : []);
    if (!filtered.length) {
      inventoryList.innerHTML = `<div class="not-found">
        <img src="https://cdn-icons-png.flaticon.com/512/2748/2748558.png" width="120">
        <p>😢 No items found</p>
      </div>`;
    }
  }

  searchInput.addEventListener("input", applyFilters);
  filterCategory.addEventListener("change", applyFilters);
  filterStatus.addEventListener("change", applyFilters);

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