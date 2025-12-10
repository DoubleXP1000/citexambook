<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy the session completely
session_destroy();

// Redirect back to login page with a message
header("Location: login_new.php?logout=1");
exit();
?>