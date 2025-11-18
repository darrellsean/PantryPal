<?php
require_once 'config.php';
require_login();

$stmt = $mysqli->prepare("DELETE FROM notifications WHERE user_id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
?>
