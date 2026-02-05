<?php
include('cp_connect.php');

// --- Fetch today's data ---
$today = date('Y-m-d');
$notifications = [];

// Fetch new complaints
$res = $conn->query("SELECT * FROM report WHERE DATE(time_reported) = '$today'");
while ($r = $res->fetch_assoc()) {
    $notifications[] = [
        'type' => 'complaint',
        'message' => "New complaint: {$r['type_of_crime']}",
        'time' => date("h:i A", strtotime($r['time_reported']))
    ];
}

// Fetch new arrestees
$res = $conn->query("SELECT * FROM arrestees WHERE DATE(arrest_time) = '$today'");
while ($r = $res->fetch_assoc()) {
    $notifications[] = [
        'type' => 'arrestee',
        'message' => "New arrestee: {$r['full_name']}",
        'time' => date("h:i A", strtotime($r['arrest_time']))
    ];
}

// Fetch new cases
$res = $conn->query("SELECT * FROM cases WHERE DATE(FiledDate) = '$today'");
while ($r = $res->fetch_assoc()) {
    $notifications[] = [
        'type' => 'case',
        'message' => "New case filed: {$r['CaseType']}",
        'time' => date("h:i A", strtotime($r['FiledDate']))
    ];
}

if (count($notifications) == 0) {
    echo "<div class='reminder-card empty'>No new notifications today.</div>";
} else {
    foreach(array_reverse($notifications) as $n) {
        $color = $n['type']=='complaint' ? '#1E90FF' : ($n['type']=='arrestee' ? '#28a745' : '#FF8C00');
        $icon  = $n['type']=='complaint' ? 'report' : ($n['type']=='arrestee' ? 'person' : 'gavel');

        echo "<div class='reminder-card' style='border-left:5px solid $color;'>
                <span class='material-icons-sharp' style='color:$color; font-size:24px; vertical-align:middle;'>$icon</span>
                <div class='reminder-text'>
                    <p>{$n['message']}</p>
                    <small>{$n['time']}</small>
                </div>
              </div>";
    }
}
?>
