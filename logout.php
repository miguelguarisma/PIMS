<?php
session_start();

// destroy session
session_unset();
session_destroy();

// maglagay ng success message
session_start();
$_SESSION['logout_message'] = "✅ You have been logged out successfully!";

// balik sa login page
header("Location: index.php");
exit();
?>
