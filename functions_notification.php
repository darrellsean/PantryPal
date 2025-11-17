<?php
// functions_notification.php
require_once 'config.php';

/**
 * Add a notification for a user.
 *
 * @param int $user_id
 * @param string $title
 * @param string $message
 * @param string|null $type           (optional: "inventory", "expiry", "donation", "meal")
 * @param string|null $link           (optional: which page it links to)
 */
function addNotification($user_id, $title, $message, $type = null, $link = null) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        INSERT INTO notifications (user_id, title, message, type, link)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $user_id, $title, $message, $type, $link);
    $stmt->execute();
}


/**
 * Get all notifications for a user
 */
function getNotifications($user_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT notification_id, title, message, is_read, type, link, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


/**
 * Mark ONE notification read/unread
 */
function markNotificationRead($notification_id, $user_id, $is_read) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        UPDATE notifications
        SET is_read = ?
        WHERE notification_id = ? AND user_id = ?
    ");
    $stmt->bind_param("iii", $is_read, $notification_id, $user_id);
    $stmt->execute();
}


/**
 * Delete ONE notification
 */
function deleteNotification($notification_id, $user_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        DELETE FROM notifications
        WHERE notification_id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
}


/**
 * Delete ALL notifications for a user
 */
function deleteAllNotifications($user_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM notifications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

?>
