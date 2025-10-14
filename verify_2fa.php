<?php
session_start();
include("../loginregister/connect.php");

if(!isset($_GET['email'])){
  die("Invalid request.");
}

$email = $_GET['email'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $enteredCode = $_POST['code'];

  $stmt = $conn->prepare("SELECT code, expires_at FROM twofa_codes WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if($row){
    if($row['code'] == $enteredCode && strtotime($row['expires_at']) > time()){
      // Enable 2FA for user
      $update = $conn->prepare("UPDATE users SET twofa_enabled=1 WHERE email=?");
      $update->bind_param("s", $email);
      $update->execute();

      echo "<script>alert('2FA enabled successfully!'); window.location.href='../settings/settings.php';</script>";
    } else {
      echo "<script>alert('Invalid or expired code.');</script>";
    }
  } else {
    echo "<script>alert('No verification request found.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify 2FA</title>
  <link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap' rel='stylesheet'>
  <style>
    body { font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f5f5f5; }
    .verify-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
    input { padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; width: 100%; text-align: center; font-size: 18px; }
    button { padding: 10px 20px; background: #4CAF50; border: none; border-radius: 8px; color: white; cursor: pointer; font-size: 16px; }
    button:hover { background: #45a049; }
  </style>
</head>
<body>
  <div class="verify-box">
    <h2>Enter Verification Code</h2>
    <form method="POST">
      <input type="text" name="code" placeholder="6-digit code" maxlength="6" required>
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
