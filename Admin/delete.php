<?php
$host = "sql202.infinityfree.com";
$user = "if0_40422239";
$password = "miguel122004";
$database = "if0_40422239_db_pims";

$connection = new mysqli($host, $user, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check kung may ID na pinasa
if (!isset($_GET["id"])) {
    header("Location: ../Admin/user_management.php");
    exit;
}

$id = $_GET["id"];

// DELETE query
$sql = "DELETE FROM users WHERE id = $id";
$result = $connection->query($sql);

if ($result) {
    header("Location: ../Admin/user_management.php?msg=deleted");
    exit;
} else {
    die("Error deleting record: " . $connection->error);
}

?>