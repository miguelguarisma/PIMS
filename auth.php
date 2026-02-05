<?php
session_start();

// Protektahan ang page kung walang naka-login
if (!isset($_SESSION['email'])) {
    $_SESSION['login_error'] = "❌ Please login first!";
    header("Location: ../index.php"); // adjust path depende sa location ng login page
    exit();
}
?>
