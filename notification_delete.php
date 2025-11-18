<?php
require_once 'config.php';
require_login();

$id = $_POST['id'];

$stmt = $mysqli->prepare("DELETE FROM notifications WHERE notification_id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
?>
