<?php
include '../loginregister/connect.php';

$email = $_GET['email'];
$code = $_GET['code'];

$stmt = $conn->prepare("SELECT * FROM verification_codes WHERE email=? AND code=? AND expiry > NOW()");
$stmt->bind_param("si", $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Valid → show new password form
    echo '<h2>Set New Password</h2>
          <form method="POST" action="reset_password.php">
            <input type="hidden" name="email" value="'.$email.'">
            <label>New Password:</label><br>
            <input type="password" name="password" required><br><br>
            <button type="submit">Save</button>
          </form>';
} else {
    echo "Invalid or expired verification code.";
}
?>
