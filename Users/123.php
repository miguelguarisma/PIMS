<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="123.css">
    <title>PIMS - Complaints Dashboard</title>
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
                <a href="123.php" class="active">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Complaints</h3>
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
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <!-- Complaints Table -->
            <div class="recent-orders">
    <h2>Complaints</h2>
     <table>
        <thead>
            <tr>
                <th>Case No.</th>
                <th>Reported By</th>
                <th>Type of Crime</th>
                <th>Location</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Details</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include("db_connect.php");
            $sql = "SELECT * FROM report ORDER BY complaint_id DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['case_number']) ?></td>
                        <td><?= htmlspecialchars($row['reported_by']) ?></td>
                        <td><?= htmlspecialchars($row['type_of_crime']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['date_reported']) ?></td>
                        <td><?= htmlspecialchars($row['time_reported']) ?></td>
                        <td>
                            <?php 
                                  $status = strtolower($row['status'] ?? 'open');
                                   $class = "status-open"; 
                                  if ($status === "resolved") $class = "status-resolved";
                                   elseif ($status === "under investigation") $class = "status-investigating";
                                  ?>
                                   <span class="status-badge <?= $class ?>">
                                     <?= htmlspecialchars($row['status'] ?? 'Open') ?>

                                     </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['details']) ?></td>
                                    <td>
                                     <td>
                                     <div class="action-buttons">
                                         <a href="edit_complaint.php?id=<?= urlencode($row['complaint_id']) ?>" class="edit" title="Edit">
                                          <span class="material-icons-sharp">edit</span> </a>        
                                     </div>
                                    </td>
                                </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No complaints filed yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="#">Show All</a>
</div>
    </div>

 
            <!-- End of Complaints Table -->
        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Migz</b></p>
                        <small class="text-muted">User</small>
                    </div>
                    <div class="profile-photo">
                        <img src="images/profile-1.jpg">
                    </div>
                </div>
            </div>
            <!-- End of Nav -->
        </div>
    </div>

    <script src="123.js"></script>
</body>
</html>
