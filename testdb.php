<?php
require_once 'config.php';

if ($mysqli) {
    echo "<h2 style='color:green;'>✅ Database connection successful!</h2>";
} else {
    echo "<h2 style='color:red;'>❌ Database connection failed!</h2>";
}
?>
