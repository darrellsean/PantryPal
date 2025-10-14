<?php
session_start();
 use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../vendor/autoload.php';
include '../loginregister/connect.php'; // DB connection

// Make sure the user is logged in and email exists in session
if (!isset($_SESSION['email'])) {
    die("❌ No user email found in session. Please log in first.");
}

$email = $_SESSION['email']; 
$code = rand(100000, 999999); // generate 6-digit code
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Insert into verification_codes table
$stmt = $conn->prepare("INSERT INTO verification_codes (user_email, verification_code, expires_at) 
                        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$stmt->bind_param("ss", $email, $code);

if ($stmt->execute()) {
    // Load PHPMailer
 // after composer require phpmailer/phpmailer

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seanwarkey@gmail.com'; // your Gmail
        $mail->Password   = 'khmq mcje hyod ucfs';   // Gmail App Password (not your real password)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('yourgmail@gmail.com', 'PantryPal');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your PantryPal Verification Code";
        $mail->Body    = "Hello, here is your 6-digit verification code: <b>$code</b>. It expires in 10 minutes.";

        $mail->send();
        echo "✅ Verification email sent!";
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "❌ Database Error: " . $conn->error;
}
?>
