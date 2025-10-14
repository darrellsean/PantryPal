<?php
session_start();
include("../loginregister/connect.php");

if (!isset($_SESSION['email'])) {
  echo "Not logged in.";
  exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = trim($_POST['code']);

  // Fetch the latest 2FA code for this user
 $stmt = $conn->prepare("SELECT code, expires_at FROM twofa_codes WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $storedCode = trim($row['code']);
    $expiresAt = strtotime($row['expires_at']);
    $currentTime = time();

    if ($currentTime <= $expiresAt && $code === $storedCode) {
      // ✅ Valid code — enable 2FA
      $update = $conn->prepare("UPDATE users SET twofa_enabled = 1 WHERE email=?");
      $update->bind_param("s", $email);
      $update->execute();

      // Delete the used code
      $del = $conn->prepare("DELETE FROM twofa_codes WHERE email=?");
      $del->bind_param("s", $email);
      $del->execute();

      // ✅ Success message before redirect
      echo "
      <html>
      <head>
        <meta http-equiv='refresh' content='2;url=settings.php?2fa=enabled'>
        <style>
          body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
          }
          .success-box {
            background: #fff;
            padding: 25px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
          }
          .success-box h2 {
            color: #28a745;
          }
          .success-box p {
            color: #555;
          }
        </style>
      </head>
      <body>
        <div class='success-box'>
          <h2>✅ 2FA Enabled Successfully!</h2>
          <p>Redirecting to your settings page...</p>
        </div>
      </body>
      </html>";
      exit();
    } else {
      $error = "Invalid or expired code.";
    }
  } else {
    $error = "No code found for this user.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify 2FA - PantryPal</title>
  <link rel="stylesheet" href="settings.css">
  <style>
    body {
      background: #f5f5f5;
      font-family: "Montserrat", sans-serif;
    }
    .verify-container {
      width: 350px;
      margin: 100px auto;
      text-align: center;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    input {
      width: 80%;
      padding: 10px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      text-align: center;
      font-size: 16px;
    }
    button {
      margin-top: 15px;
      padding: 10px 20px;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #218838;
    }
  </style>
</head>
<body>
  <div class="verify-container">
    <h2>Enter your 6-digit code</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="code" maxlength="6" required placeholder="Enter code">
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
