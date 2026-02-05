<?php
include('cp_connect.php');
$id = $_GET['id'];

$conn->query("UPDATE report SET is_read = 1 WHERE complaint_id = $id");
header("Location: admin_page.php");
exit();
?>