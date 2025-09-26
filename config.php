<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = ''; // Default empty in XAMPP
$DB_NAME = 'pantrypal';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno){
    echo "Database Connection Failec: ".$mysqli->connect_error;
    exit;
}
$mysqli->set_charset('utf8mb4');

// Require user login (for demo we can skip this im guessing)
function require_login(){
    if(empty($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1;
    }
}
?>