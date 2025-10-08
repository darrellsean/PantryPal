<?php
session_start();        // start session before destroying it
session_unset();        // clear all session variables
session_destroy();      // destroy session

// Redirect back to login page
header("Location: login.php");
exit();
