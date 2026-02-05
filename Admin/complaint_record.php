<?php
session_start();
include(__DIR__ . '/cp_connect.php');

// --- Fetch all complaints
$sql = "SELECT * FROM report ORDER BY date_reported DESC, time_reported DESC";
$result = $conn->query($sql);

// --- Admin info
$adminName = "Migz";
$adminRole = "Admin";
$adminImg = "img/migz.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Complaint Records</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="admin.css">

<style>
:root {
    --sidebar-width: 220px;
}

/* --- BACKGROUND --- */
body {
    background: url('../pnp2.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Sidebar fixed */
aside {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background-color: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    z-index: 1000;
}

/* Sidebar styles */
aside .toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
}

aside .toggle .logo {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

aside .toggle .logo img {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
}

aside .sidebar {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    padding-top: 1rem;
}

aside .sidebar a {
    display: flex;
    align-items: center;
    color: #191818ff;
    height: 3.7rem;
    gap: 1rem;
    padding-left: 1.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

aside .sidebar a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Right profile */
.right-profile {
    position: fixed;
    top: 5px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 1001;
    color: #fff;
}

.right-profile img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

/* Main content */
.main-content {
    margin-left: var(--sidebar-width);
    padding: 30px;
}

/* Glassmorphism Table Container */
.table-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 15px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    color: #3b3030ff;
}

/* Table text color */
.table th {
    color: white;
    background: #292222ff;
    
    
}
 .table td {
    color: #221e1eff !important;
}

/* Scrollable */
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}
</style>
</head>

<body>

<!-- Sidebar -->
<aside>
    <div class="toggle">
        <div class="logo">
            <img src="img/logo.jpg" alt="Logo">
            <h2>PI<span class="danger">MS</span></h2>
        </div>
    </div>
    <div class="sidebar">
        <a href="../Admin/admin_page.php">
            <span class="material-icons-sharp">dashboard</span>
            <h3>Dashboard</h3>
        </a>
        <a href="complaint_record.php" class="active">
            <span class="material-icons-sharp">receipt_long</span>
            <h3>Complaint Records</h3>
        </a>
        <a href="arrested_record.php">
            <span class="material-icons-sharp">groups</span>
            <h3>Arrested Records</h3>
        </a>
        <a href="case_record.php">
            <span class="material-icons-sharp">gavel</span>
            <h3>Case Records</h3>
        </a>
        <a href="#"><span class="material-icons-sharp">insights</span><h3>Evidence Management</h3></a>
        <a href="#"><span class="material-icons-sharp">report_gmailerrorred</span><h3>Reports</h3></a>
        <a href="#"><span class="material-icons-sharp">settings</span><h3>Settings</h3></a>
        <a href="../logout.php" onclick="return confirm('Are you sure you want to logout?');">
            <span class="material-icons-sharp">logout</span><h3>Logout</h3>
        </a>
    </div>
</aside>

<!-- Right Profile -->
<div class="right-profile">
    <div class="info text-end">
        <p class="mb-0">Hey, <b><?= $adminName ?></b></p>
        <small class="text-muted"><?= $adminRole ?></small>
    </div>
    <img src="<?= $adminImg ?>" alt="Profile Photo">
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4 text-light">Complaint Records</h2>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" class="form-control bg-light" id="searchInput" placeholder="Search complaints...">
    </div>

    <!-- Glass Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover" id="complaintTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reported By</th>
                        <th>Type of Crime</th>
                        <th>Location</th>
                        <th>Date Reported</th>
                        <th>Time Reported</th>
                        <th>Status</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['complaint_id'] ?></td>
                                <td><?= htmlspecialchars($row['reported_by']) ?></td>
                                <td><?= htmlspecialchars($row['type_of_crime']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['date_reported']) ?></td>
                                <td><?= htmlspecialchars($row['time_reported']) ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'];
                                    $badgeClass = match($status) {
                                        'Open' => 'bg-danger',
                                        'Under Investigation' => 'bg-warning text-dark',
                                        'Resolved' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['details']) ?></td>
                                <td>
                                    <a href="edit_complaint.php?id=<?= $row['complaint_id'] ?>" class="btn btn-sm btn-success">Edit</a>
                                    <a href="delete_complaint.php?id=<?= $row['complaint_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-light">No complaints found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS -->
<script src="admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('#complaintTable tbody tr');
    rows.forEach(row => {
        row.style.display = Array.from(row.cells)
            .some(cell => cell.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    });
});
</script>
</body>
</html>
