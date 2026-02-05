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

/* Sidebar fixed to left */
aside {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background-color: var(--color-white);
    box-shadow: var(--box-shadow);
    z-index: 1000;
}

/* Sidebar content */
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
    color: var(--color-info-dark);
    height: 3.7rem;
    gap: 1rem;
    padding-left: 1.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

aside .sidebar a:hover {
    color: var(--color-primary);
    background-color: #f0f0f0;
}

aside .sidebar a span {
    font-size: 1.6rem;
}

aside .sidebar a:last-child {
    margin-top: auto;
    margin-bottom: 2rem;
}

/* Profile top right */
.right-profile {
    position: fixed;
    top: 5px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 1001;
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
    padding: 20px;
}


</style>
</head>
<body class="bg-light text-dark">

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
        <a href="../Admin/user_management.php">
            <span class="material-icons-sharp">person_outline</span>
            <h3>Users</h3>
        </a>
        <a href="complaint_record.php" class="active">
            <span class="material-icons-sharp">receipt_long</span>
            <h3>Complaint Records</h3>
        </a>
        <a href="arrested_record.php">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>Arrested Records</h3>
                </a>
                <a href="case_record.php">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>Case Records</h3>
                </a>
        <a href="#">
            <span class="material-icons-sharp">insights</span>
            <h3>Evidence Management</h3>
        </a>
        <a href="#">
            <span class="material-icons-sharp">report_gmailerrorred</span>
            <h3>Reports</h3>
        </a>
        <a href="#">
            <span class="material-icons-sharp">settings</span>
            <h3>Settings</h3>
        </a>
        <a href="../logout.php" onclick="return confirm('Are you sure you want to logout?');">
            <span class="material-icons-sharp">logout</span>
            <h3>Logout</h3>
        </a>
    </div>
</aside>

<!-- Right Profile -->
<div class="right-profile">
    <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>
    <div class="info text-end">
        <p class="mb-0">Hey, <b><?= $adminName ?></b></p>
        <small class="text-muted"><?= $adminRole ?></small>
    </div>
    <img src="<?= $adminImg ?>" alt="Profile Photo">
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4">Arrested Records</h2>

    <!-- Search -->
    <div class="mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search complaints...">
    </div>

    <!-- Table -->
    <table class="table table-striped table-bordered table-hover" id="complaintTable">
        <thead class="table-dark">
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
                    <a href="edit1.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-success">Edit</a>
                    <a href="delete1.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr><td colspan="10" class="text-center">No arrestees found.</td></tr>
    <?php endif; ?>
    </tbody>
    </table>
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
