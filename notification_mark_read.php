<?php
require_once 'config.php';
require_login();

$id = $_POST['id'];
$is_read = $_POST['read'];

$stmt = $mysqli->prepare("UPDATE notifications SET is_read=? WHERE notification_id=? AND user_id=?");
$stmt->bind_param("iii", $is_read, $id, $_SESSION['user_id']);
$stmt->execute();
?>
