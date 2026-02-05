<?php
session_start();
include(__DIR__ . '/cp_connect.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid Request: Missing or invalid ID.");
}

$complaint_id = intval($_GET['id']);
$errorMessage = $successMessage = "";

// Fetch complaint
$sql = "SELECT * FROM report WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Complaint not found.");
}

$complaint = $result->fetch_assoc();

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reported_by   = trim($_POST['reported_by'] ?? '');
    $type_of_crime = trim($_POST['type_of_crime'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $date_reported = $_POST['date_reported'] ?? '';
    $time_reported = $_POST['time_reported'] ?? '';
    $status        = $_POST['status'] ?? '';
    $details       = trim($_POST['details'] ?? '');

    if ($reported_by && $type_of_crime && $location && $date_reported && $time_reported && $status && $details) {
        $update_sql = "UPDATE report SET reported_by=?, type_of_crime=?, location=?, date_reported=?, time_reported=?, status=?, details=? WHERE complaint_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssi", $reported_by, $type_of_crime, $location, $date_reported, $time_reported, $status, $details, $complaint_id);

        if ($stmt->execute()) {
            $successMessage = "✅ Complaint updated successfully!";
            $stmt = $conn->prepare("SELECT * FROM report WHERE complaint_id = ?");
            $stmt->bind_param("i", $complaint_id);
            $stmt->execute();
            $complaint = $stmt->get_result()->fetch_assoc();
        } else {
            $errorMessage = "❌ Error updating complaint: " . $conn->error;
        }
    } else {
        $errorMessage = "⚠️ Please fill in all fields.";
    }
}
?>
<!-- HTML form same as previous form you shared, with Bootstrap -->
<?php

// Start session only if not started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
 }
include("cp_connect.php");

// --- Check kung may ID sa URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Invalid Request.");
}

$complaint_id = intval($_GET['id']);
$errorMessage = $successMessage = "";

// --- Fetch Complaint Data
$sql = "SELECT * FROM report WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Complaint not found.");
}

$complaint = $result->fetch_assoc();

// --- Update Complaint kapag may POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reported_by   = $_POST['reported_by'] ?? '';
    $type_of_crime = $_POST['type_of_crime'] ?? '';
    $location      = $_POST['location'] ?? '';
    $date_reported = $_POST['date_reported'] ?? '';
    $time_reported = $_POST['time_reported'] ?? '';
    $status        = $_POST['status'] ?? '';
    $details       = $_POST['details'] ?? '';

    if ($reported_by && $type_of_crime && $location && $date_reported && $time_reported && $status && $details) {
        $update_sql = "UPDATE report 
                       SET reported_by=?, type_of_crime=?, location=?, date_reported=?, time_reported=?, status=?, details=?
                       WHERE complaint_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssi", $reported_by, $type_of_crime, $location, $date_reported, $time_reported, $status, $details, $complaint_id);

        if ($stmt->execute()) {
            $successMessage = "✅ Complaint updated successfully!";
        } else {
            $errorMessage = "❌ Error updating complaint: " . $conn->error;
        }
    } else {
        $errorMessage = "⚠️ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Complaint</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Complaint</h2>

        <!-- Error Message -->
        <?php if ($errorMessage): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><?= $errorMessage ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?= $successMessage ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Complaint Form -->
        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Reported by</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="reported_by" value="<?= htmlspecialchars($complaint['reported_by']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Type of Crime</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="type_of_crime" value="<?= htmlspecialchars($complaint['type_of_crime']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Location</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($complaint['location']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Date Reported</label>
                <div class="col-sm-6">
                    <input type="date" class="form-control" name="date_reported" value="<?= htmlspecialchars($complaint['date_reported']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Time Reported</label>
                <div class="col-sm-6">
                    <input type="time" class="form-control" name="time_reported" value="<?= htmlspecialchars($complaint['time_reported']) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-6">
                    <select class="form-control" name="status" required>
                        <option value="Open" <?= ($complaint['status'] == "Open") ? "selected" : "" ?>>Open</option>
                        <option value="Under Investigation" <?= ($complaint['status'] == "Under Investigation") ? "selected" : "" ?>>Under Investigation</option>
                        <option value="Resolved" <?= ($complaint['status'] == "Resolved") ? "selected" : "" ?>>Resolved</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Details</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="details" rows="3" required><?= htmlspecialchars($complaint['details']) ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-danger" href="complaint_record.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-hide alerts after 3 seconds
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.classList.remove('show');
        alert.classList.add('fade');
        alert.style.display = 'none';
    });
}, 3000);
</script>
</body>
</html>
 