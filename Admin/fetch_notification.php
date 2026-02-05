<?php
include('cp_connect.php');

$sql = "SELECT * FROM report WHERE is_read = 0 ORDER BY date_reported DESC, time_reported DESC";
$result = $conn->query($sql);

$notifications = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notifications[] = [
            'id' => $row['complaint_id'],
            'title' => $row['type_of_crime'],
            'date' => date('M d, Y', strtotime($row['date_reported'])),
            'time' => $row['time_reported']
        ];
    }
}

echo json_encode($notifications);
?>
