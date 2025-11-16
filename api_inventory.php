<?php
require_once 'config.php';
require_login();
require_once 'functions_notification.php';

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

/*
|--------------------------------------------------------------------------
| LIST ITEMS
|--------------------------------------------------------------------------
*/
if ($action === 'list') {
    $stmt = $mysqli->prepare("SELECT * FROM food_item WHERE user_id=? ORDER BY expiry_date ASC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['items' => $items]);
    exit;
}

/*
|--------------------------------------------------------------------------
| ADD / UPDATE ITEM
|--------------------------------------------------------------------------
*/
if ($action === 'save') {
    $item_id = $_POST['item_id'] ?? null;
    $name = $_POST['item_name'];
    $cat = $_POST['category'];
    $qty = $_POST['quantity'];
    $exp = $_POST['expiry_date'];

    if ($item_id) {
        // Update existing item
        $stmt = $mysqli->prepare("UPDATE food_item 
            SET item_name=?, category=?, quantity=?, expiry_date=? 
            WHERE item_id=? AND user_id=?");
        $stmt->bind_param('ssssii', $name, $cat, $qty, $exp, $item_id, $user_id);
        $stmt->execute();

        // Notification: item updated
        addNotification(
            $user_id,
            "Item updated",
            "You updated '{$name}' in your inventory.",
            "inventory",
            "inventory.php"
        );

    } else {
        // Insert new item
        $stmt = $mysqli->prepare("INSERT INTO food_item (user_id, item_name, category, quantity, expiry_date) 
                                  VALUES (?,?,?,?,?)");
        $stmt->bind_param('issss', $user_id, $name, $cat, $qty, $exp);
        $stmt->execute();

        // Notification: item added
        addNotification(
            $user_id,
            "New item added",
            "'{$name}' was added to your inventory.",
            "inventory",
            "inventory.php"
        );
    }

    // Check for expiry warning
    if (!empty($exp)) {
        $days = (strtotime($exp) - time()) / (60 * 60 * 24);

        if ($days <= 3 && $days >= 0) {
            addNotification(
                $user_id,
                "Item expiring soon",
                "Your item '{$name}' will expire in {$days} day(s).",
                "inventory",
                "inventory.php?filter=expiring"
            );
        }
    }

    echo json_encode(['ok' => true]);
    exit;
}

/*
|--------------------------------------------------------------------------
| GET ITEM BY ID
|--------------------------------------------------------------------------
*/
if ($action === 'get') {
    $id = $_GET['id'];

    $stmt = $mysqli->prepare("SELECT * FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    echo json_encode(['item' => $item]);
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE ITEM
|--------------------------------------------------------------------------
*/
if ($action === 'delete') {
    $id = $_POST['id'];

    // Get item name for notification
    $stmt = $mysqli->prepare("SELECT item_name FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $item_name = $item['item_name'] ?? 'Item';

    // Delete item
    $stmt = $mysqli->prepare("DELETE FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();

    // Notification: item deleted
    addNotification(
        $user_id,
        "Item removed",
        "'{$item_name}' was deleted from your inventory.",
        "inventory",
        "inventory.php"
    );

    echo json_encode(['ok' => true]);
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE ITEM STATUS
|--------------------------------------------------------------------------
*/
if ($action === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Get item name for notification message
    $stmt = $mysqli->prepare("SELECT item_name FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $item_name = $item['item_name'] ?? 'Item';

    // Update status
    $stmt = $mysqli->prepare("UPDATE food_item SET status=? WHERE item_id=? AND user_id=?");
    $stmt->bind_param('sii', $status, $id, $user_id);
    $stmt->execute();

    // Create notifications depending on status
    if ($status === "For Donation") {
        addNotification(
            $user_id,
            "Donation item ready",
            "'{$item_name}' was marked as For Donation.",
            "donation",
            "inventory.php?filter=donation"
        );
    }

    if ($status === "For Meal") {
        addNotification(
            $user_id,
            "Meal planning update",
            "'{$item_name}' is planned for a meal.",
            "meal",
            "inventory.php?filter=meal"
        );
    }

    if ($status === "Expired") {
        addNotification(
            $user_id,
            "Expired item",
            "'{$item_name}' has expired.",
            "inventory",
            "inventory.php?filter=expired"
        );
    }

    echo json_encode(['ok' => true]);
    exit;
}
?>
