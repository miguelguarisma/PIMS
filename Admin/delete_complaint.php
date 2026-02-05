<?php
session_start();
include(__DIR__ . '/cp_connect.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid Request: Missing or invalid ID.");
}

$complaint_id = intval($_GET['id']);

// Delete complaint
$stmt = $conn->prepare("DELETE FROM report WHERE complaint_id = ?");
$stmt->bind_param("i", $complaint_id);

if ($stmt->execute()) {
    header("Location: complaint_record.php?msg=deleted");
    exit;
} else {
    die("❌ Failed to delete complaint: " . $conn->error);
}
?>
