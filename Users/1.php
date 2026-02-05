<?php
session_start();
include("db_connect.php");

// Initialize variables para iwas undefined error
$reported_by = $type_of_crime = $location = $date_reported = $time_reported = $details = "";
$errorMessage = $successMessage = "";

// ===== Add Complaint =====
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_number   = "CASE-" . date("Y") . "-" . rand(1000,9999);
    $reported_by   = $_POST['reported_by'] ?? '';
    $type_of_crime = $_POST['type_of_crime'] ?? '';
    $location      = $_POST['location'] ?? '';
    $date_reported = $_POST['date_reported'] ?? '';
    $time_reported = $_POST['time_reported'] ?? '';
    $details       = $_POST['details'] ?? '';

    if ($reported_by && $type_of_crime && $location && $date_reported && $time_reported && $details) {
        $sql = "INSERT INTO report (case_number, reported_by, type_of_crime, location, date_reported, time_reported, details)
                VALUES ('$case_number', '$reported_by', '$type_of_crime', '$location', '$date_reported', '$time_reported', '$details')";

        if ($conn->query($sql) === TRUE) {
            $successMessage = "✅ Complaint filed successfully! Case No: " . $case_number;
            // Clear form values after success
            $reported_by = $type_of_crime = $location = $date_reported = $time_reported = $details = "";
        } else {
            $errorMessage = "❌ Error: " . $conn->error;
        }
    } else {
        $errorMessage = "⚠️ Please fill in all fields.";
    }
}

// ===== Fetch Complaints (optional, kung meron kang created_at column) =====
// $sql = "SELECT * FROM report ORDER BY created_at DESC";
// $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Create Complaint</h2>

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
                    <input type="text" class="form-control" name="reported_by" value="<?= htmlspecialchars($reported_by) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Type of Crime</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="type_of_crime" value="<?= htmlspecialchars($type_of_crime) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Location</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($location) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Date Reported</label>
                <div class="col-sm-6">
                    <input type="date" class="form-control" name="date_reported" value="<?= htmlspecialchars($date_reported) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Time Reported</label>
                <div class="col-sm-6">
                    <input type="time" class="form-control" name="time_reported" value="<?= htmlspecialchars($time_reported) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Details</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="details" rows="3" required><?= htmlspecialchars($details) ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
               </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="../Users/123.php" role="button">Cancel</a>
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
