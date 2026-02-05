<?php
session_start();
include(__DIR__ . '/cp_connect.php');

// Check kung may ID na nakuha
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Invalid request.");
}

$id = intval($_GET['id']); // sanitize

// Query para i-delete ang record
$sql = "DELETE FROM arrestees WHERE arrestee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Kapag success, balik sa arrested records page
    header("Location: arrested_record.php?msg=deleted");
    exit;
} else {
    echo "❌ Error deleting record: " . $conn->error;
}
?>
