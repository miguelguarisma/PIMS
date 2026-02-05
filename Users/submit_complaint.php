<?php
session_start();
include('cp_connect.php');

$case_number = $_POST['case_number'];
$reported_by = $_POST['reported_by'];
$type_of_crime = $_POST['type_of_crime'];
$location = $_POST['location'];
$date_reported = $_POST['date_reported'];
$time_reported = $_POST['time_reported'];
$details = $_POST['details'];

$stmt = $conn->prepare("INSERT INTO report (case_number, reported_by, type_of_crime, location, date_reported, time_reported, details, status, is_read) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 0)");
$stmt->bind_param("sssssss", $case_number, $reported_by, $type_of_crime, $location, $date_reported, $time_reported, $details);
$stmt->execute();

header("Location: user_dashboard.php?msg=Complaint submitted!");
exit();
?>
