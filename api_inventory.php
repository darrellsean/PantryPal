<?php
require_once 'config.php';
require_once 'functions_notification.php'; // ⬅ needed for addNotification()
require_login();

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';


if ($action === 'list') {
    $stmt = $mysqli->prepare("SELECT * FROM food_item WHERE user_id=? ORDER BY expiry_date ASC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['items'=>$items]);
    exit;
}


if ($action === 'save') {
    $item_id = $_POST['item_id'] ?? null;
    $name = $_POST['item_name'];
    $cat = $_POST['category'];
    $qty = $_POST['quantity'];
    $exp = $_POST['expiry_date'];

    // New item
    if (!$item_id) {
        $stmt = $mysqli->prepare("INSERT INTO food_item (user_id, item_name, category, quantity, expiry_date) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issss', $user_id, $name, $cat, $qty, $exp);
        $stmt->execute();

        
        addNotification(
            $user_id,
            "New Inventory Item Added",
            "$name has been added to your inventory.",
            "inventory",
            "inventory.php"
        );

    } 
    // update item
    else {
        $stmt = $mysqli->prepare("UPDATE food_item SET item_name=?, category=?, quantity=?, expiry_date=? WHERE item_id=? AND user_id=?");
        $stmt->bind_param('ssssii', $name, $cat, $qty, $exp, $item_id, $user_id);
        $stmt->execute();

        // 🔔 Notification – item updated
        addNotification(
            $user_id,
            "Inventory Item Updated",
            "$name has been updated.",
            "inventory",
            "inventory.php"
        );
    }

    /* Expiring within 3 days → create notification */
    $days_left = (strtotime($exp) - strtotime("today")) / 86400;
    if ($days_left <= 3 && $days_left >= 0) {
        addNotification(
            $user_id,
            "Item Expiring Soon",
            "$name will expire in $days_left day(s).",
            "expiry",
            "inventory.php?filter=expiring"
        );
    }

    /* Quantity = 0 → out of stock */
    if ((int)$qty === 0) {
        addNotification(
            $user_id,
            "Item Out of Stock",
            "$name has reached 0 quantity.",
            "inventory",
            "inventory.php"
        );
    }

    echo json_encode(['ok'=>true]);
    exit;
}


if ($action === 'get') {
    $id = $_GET['id'];
    $stmt = $mysqli->prepare("SELECT * FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    echo json_encode(['item'=>$item]);
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'];

    // find name first
    $tmp = $mysqli->prepare("SELECT item_name FROM food_item WHERE item_id=? AND user_id=?");
    $tmp->bind_param("ii", $id, $user_id);
    $tmp->execute();
    $name = $tmp->get_result()->fetch_assoc()['item_name'];

    $stmt = $mysqli->prepare("DELETE FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();

    // 🔔 Notification – item deleted
    addNotification(
        $user_id,
        "Item Deleted",
        "$name was removed from your inventory.",
        "inventory",
        "inventory.php"
    );

    echo json_encode(['ok'=>true]);
    exit;
}

/* ============================
   5. UPDATE STATUS
============================ */
if ($action === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("UPDATE food_item SET status=? WHERE item_id=? AND user_id=?");
    $stmt->bind_param('sii', $status, $id, $user_id);
    $stmt->execute();

    // if user marks as donation
    if ($status === "donation") {
        addNotification(
            $user_id,
            "Donation Item Ready",
            "You marked an item for donation — go to your donation list.",
            "donation",
            "inventory.php?filter=donation"
        );
    }

    echo json_encode(['ok'=>true]);
    exit;
}

?>
