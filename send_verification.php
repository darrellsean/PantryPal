<?php
session_start();
include("../loginregister/connect.php");
require '../vendor/autoload.php'; // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['email'])) {
  echo json_encode(["status" => "error", "message" => "User not logged in."]);
  exit();
}

$email = $_SESSION['email'];
$code = rand(100000, 999999); // 6-digit verification code
$expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Store code in database
$stmt = $conn->prepare("INSERT INTO twofa_codes (email, code, expires_at) VALUES (?, ?, ?)
  ON DUPLICATE KEY UPDATE code=?, expires_at=?");
$stmt->bind_param("sssss", $email, $code, $expires, $code, $expires);
$stmt->execute();
$stmt->close();

// Verification link
$link = "http://localhost/pantrypal/settings/verify_2fa.php";


$mail = new PHPMailer(true);

try {
  // Server settings
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'tffbruv@gmail.com';
  $mail->Password = 'uzkx ayty xsbx huat'; // Gmail App Password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  // Recipients
  $mail->setFrom('tffbruv@gmail.com', 'PantryPal');
  $mail->addAddress($email);

  // Content
  $mail->isHTML(true);
  $mail->Subject = 'PantryPal - Verify Your 2FA';
  $mail->Body = "
    <h2>Two-Factor Authentication Verification</h2>
    <p>Your 6-digit verification code is:</p>
    <h1 style='color:#4CAF50;'>$code</h1>
    <p>This code will expire in 10 minutes.</p>
    <p>Click below to verify your account:</p>
    <a href='$link' style='color:#4CAF50;'>Verify My 2FA</a>
  ";

  // Send mail
  if ($mail->send()) {
    echo json_encode(["status" => "success", "message" => "Verification email sent to $email."]);
  } else {
    echo json_encode(["status" => "error", "message" => "Email not sent."]);
  }

} catch (Exception $e) {
  // Clear error message
  echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
