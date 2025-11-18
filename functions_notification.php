<?php
require_once 'config.php';

function addNotification($user_id, $title, $message, $type = 'general', $link = null) {
    global $conn;

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type, link)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $user_id, $title, $message, $type, $link);
    $stmt->execute();
    $stmt->close();
}
?>
