<?php
require_once 'config.php';
require_once 'functions_notification.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'list') {
    $stmt = $mysqli->prepare("SELECT item_id, item_name, category, quantity, expiry_date FROM food_item WHERE user_id = ? ORDER BY expiry_date ASC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['items'=>$items]);
    exit;
}

if ($action === 'get_plans') {
    $week_start = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
    $stmt = $mysqli->prepare("SELECT plan_id, user_id, week_start, day, meal_type, custom_meal_name FROM meal_plans WHERE user_id = ? AND week_start = ?");
    $stmt->bind_param('is', $user_id, $week_start);
    $stmt->execute();
    $plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['plans'=>$plans]);
    exit;
}

if ($action === 'save_plan') {
    $data = json_decode(file_get_contents('php://input'), true);
    $week_start = $data['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
    $plans = $data['plans'] ?? [];

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $week_start)) {
        echo json_encode(['ok'=>false, 'error'=>'Invalid week_start']);
        exit;
    }

    $mysqli->begin_transaction();
    try {
        // 1. Delete old meal plans for the week
        $del = $mysqli->prepare("DELETE FROM meal_plans WHERE user_id = ? AND week_start = ?");
        $del->bind_param('is', $user_id, $week_start);
        $del->execute();

        // 2. Insert new meal plans
        $ins = $mysqli->prepare("INSERT INTO meal_plans (user_id, week_start, day, meal_type, custom_meal_name) VALUES (?,?,?,?,?)");
        foreach ($plans as $p) {
            $day = $p['day'] ?? '';
            $meal_type = $p['meal_type'] ?? '';
            $name = $p['custom_meal_name'] ?? '';
            $ins->bind_param('issss', $user_id, $week_start, $day, $meal_type, $name);
            $ins->execute();

            // 3. Optionally reserve ingredients in inventory
            // Example: decrement quantity if item name matches food_item (simple heuristic)
            $stmtItem = $mysqli->prepare("UPDATE food_item SET quantity = GREATEST(quantity - 1, 0) WHERE user_id = ? AND item_name LIKE CONCAT('%', ?, '%')");
            $stmtItem->bind_param('is', $user_id, $name);
            $stmtItem->execute();
        }

        addNotification(
            $user_id,
            "Weekly Meal Plan Saved",
            "Your meal plan for the week starting {$week_start} has been saved.",
            "meal",
            "meal_planner.php"
        );



        $mysqli->commit();
        echo json_encode(['ok'=>true]);
    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
    }
    exit;
}

echo json_encode(['ok'=>false, 'error'=>'Invalid action']);
exit;
