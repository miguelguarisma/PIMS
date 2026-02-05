<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
    <title>Responsive Dashboard Design #1 | AsmrProg</title>
</head>

<body>

    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>Asmr<span class="danger">Prog</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="user_page.php">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="123.php">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Complaints</h3>
                </a>
                <a href="arrested.php">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>Arrested</h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Analytics</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                    <span class="message-count">27</span>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Sale List</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        report_gmailerrorred
                    </span>
                    <h3>Reports</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>New Login</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Analytics</h1>
            <!-- Analyses -->
            <div class="analyse">
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Sales</h3>
                            <h1>$65,024</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>+81%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="visits">
                    <div class="status">
                        <div class="info">
                            <h3>Site Visit</h3>
                            <h1>24,981</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>-48%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Searches</h3>
                            <h1>14,147</h1>
                        </div>
                        <div class="progresss">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                <p>+21%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Analyses -->

            <!-- New Users Section -->
            <div class="new-users">
                <h2>Add  complaints</h2>
                <a href="123.php">
                <div class="recent-orders">
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
                
            </tr>
        </thead>
        <tbody>
    <?php
    include("db_connect.php");
    // Show only the latest 3 complaints
    $sql = "SELECT * FROM report ORDER BY complaint_id DESC LIMIT 2";
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
                    <div class="action-buttons">
                        <a href="edit_complaint.php?id=<?= urlencode($row['complaint_id']) ?>" class="edit" title="Edit">
                            <span class="material-icons-sharp">edit</span>
                        </a>
                    </div>
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
            <!-- End of New Users Section -->

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Arrested</h2>
                <table class="table table-bordered">
    <thead class="table-light">
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
    $sql = "SELECT * FROM arrestees ORDER BY arrestee_id DESC LIMIT 2";
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
                    <a href="edit_arrestee.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-success">Edit</a>
                    <a href="delete_arrestee.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr><td colspan="10" class="text-center">No arrestees found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
                <a href="edit_arrestee.php">Show All</a>
            </div>
            <!-- End of Recent Orders -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Reza</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="images/profile-1.jpg">
                    </div>
                </div>

            </div>
            <!-- End of Nav -->

            <div class="user-profile">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>AsmrProg</h2>
                    <p>Fullstack Web Developer</p>
                </div>
            </div>

            <div class="reminders">
                <div class="header">
                    <h2>Reminders</h2>
                    <span class="material-icons-sharp">
                        notifications_none
                    </span>
                </div>

                <div class="notification">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            volume_up
                        </span>
                    </div>
                    
                    <div class="content">
                        <a href="1.php">
                        <div class="info">
                            <h3>Create Complaints</h3>
                        </div>
                        </a>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>

                <div class="notification deactive">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            edit
                        </span>
                    </div>
                    <a href="edit_arrestee.php">
                    <div class="content">
                        <div class="info">
                            
                            <h3>Create arrestee</h3>
                            
                        </div>
                        </a>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>

                <div class="notification add-reminder">
                    <div>
                        <span class="material-icons-sharp">
                            add
                        </span>
                        <h3>Add Reminder</h3>
                    </div>
                </div>

            </div>

        </div>


    </div>

    <script src="user.js"></script>
    <script src="index.js"></script>
</body>

</html>