<?php
session_start();
include("db_connect.php");

// Variables
$full_name = $age = $gender = $address = $arrest_date = $arrest_time = $offense_committed = $status = $remarks = "";
$errorMessage = $successMessage = "";

// ===== Add Arrestee =====
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name         = $_POST['full_name'] ?? '';
    $age               = $_POST['age'] ?? '';
    $gender            = $_POST['gender'] ?? '';
    $address           = $_POST['address'] ?? '';
    $arrest_date       = $_POST['arrest_date'] ?? '';
    $arrest_time       = $_POST['arrest_time'] ?? '';
    $offense_committed = $_POST['offense_committed'] ?? '';
    $status            = $_POST['status'] ?? 'Under Investigation';
    $remarks           = $_POST['remarks'] ?? '';

    if ($full_name && $age && $gender && $address && $arrest_date && $arrest_time && $offense_committed) {
        $sql = "INSERT INTO arrestees 
            (full_name, age, gender, address, arrest_date, arrest_time, offense_committed, status, remarks)
            VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssssss", $full_name, $age, $gender, $address, $arrest_date, $arrest_time, $offense_committed, $status, $remarks);

        if ($stmt->execute()) {
            $successMessage = "✅ Arrestee record added successfully!";
        } else {
            $errorMessage = "❌ Error: " . $conn->error;
        }
    } else {
        $errorMessage = "⚠️ Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS - Arrestees</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="123.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>PI<span class="danger">MS</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="user_page.php">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="complaints.php">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Complaints/Report</h3>
                </a>
                <a href="arrestees.php" class="active">
                    <span class="material-icons-sharp">gavel</span>
                    <h3>Arrestees</h3>
                </a>
                <a href="history.php">
                    <span class="material-icons-sharp">receipt_long</span>
                    <h3>History</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">insights</span>
                    <h3>Analytics</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar -->

        <!-- Main Content -->
        <main>
            <div class="recent-orders">
                <h2>Arrestee Records</h2>
    
                <!-- Table -->
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Offense</th>
                            <th>Status</th>
                            <th>Arrest Date</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM arrestees ORDER BY arrestee_id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['arrestee_id'] ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= $row['age'] ?></td>
                                <td><?= $row['gender'] ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= htmlspecialchars($row['offense_committed']) ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['arrest_date'] . " " . $row['arrest_time'] ?></td>
                                <td><?= htmlspecialchars($row['remarks']) ?></td>
                                <td>
                                    <a href="edit_arrestee.php?id=<?= $row['arrestee_id'] ?>" class="btn btn-sm btn-success">Edit</a>
                                    <a href="delete_arrestee.php?id=<?= $row['arrestee_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="10" style="text-align:center;">No arrestees found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End of Main -->
    </div>

    <script src="123.js"></script>
</body>
</html>
