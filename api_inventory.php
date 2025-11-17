<?php
require_once 'config.php';
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

    if ($item_id) {
        $stmt = $mysqli->prepare("UPDATE food_item SET item_name=?, category=?, quantity=?, expiry_date=? WHERE item_id=? AND user_id=?");
        $stmt->bind_param('ssssii', $name, $cat, $qty, $exp, $item_id, $user_id);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO food_item (user_id, item_name, category, quantity, expiry_date) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issss', $user_id, $name, $cat, $qty, $exp);
        $stmt->execute();
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
    $stmt = $mysqli->prepare("DELETE FROM food_item WHERE item_id=? AND user_id=?");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    echo json_encode(['ok'=>true]);
    exit;
}

// 🔹 New: Update status
if ($action === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $mysqli->prepare("UPDATE food_item SET status=? WHERE item_id=? AND user_id=?");
    $stmt->bind_param('sii', $status, $id, $user_id);
    $stmt->execute();
    echo json_encode(['ok'=>true]);
    exit;
}
