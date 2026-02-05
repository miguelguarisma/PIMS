<?php
session_start();
include("db_connect.php");

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

    $sql = "INSERT INTO arrestees 
        (full_name, age, gender, address, arrest_date, arrest_time, offense_committed, status, remarks)
        VALUES (?,?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssssss", $full_name, $age, $gender, $address, $arrest_date, $arrest_time, $offense_committed, $status, $remarks);

        if ($stmt->execute()) {
            $successMessage = "✅ Arrestee record added successfully!";
            $full_name = $age = $gender = $address = $arrest_date = $arrest_time = $offense_committed = $remarks = "";
        } else {
            $errorMessage = "❌ Error: " . $conn->error;
        }
    } else {
        $errorMessage = "⚠️ Please fill in all required fields.";
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIMS - Arrestees</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <h2>Arrestee Management</h2>

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

    <!-- Input Form -->
    <form method="post" class="mb-4">
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Full Name</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Age</label>
            <div class="col-sm-6">
                <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($age) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Gender</label>
            <div class="col-sm-6">
                <select class="form-control" name="gender" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male" <?= ($gender=="Male")?"selected":"" ?>>Male</option>
                    <option value="Female" <?= ($gender=="Female")?"selected":"" ?>>Female</option>
                    <option value="Other" <?= ($gender=="Other")?"selected":"" ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Address</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($address) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Arrest Date</label>
            <div class="col-sm-6">
                <input type="date" class="form-control" name="arrest_date" value="<?= htmlspecialchars($arrest_date) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Arrest Time</label>
            <div class="col-sm-6">
                <input type="time" class="form-control" name="arrest_time" value="<?= htmlspecialchars($arrest_time) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Offense Committed</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="offense_committed" value="<?= htmlspecialchars($offense_committed) ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Status</label>
            <div class="col-sm-6">
                <select class="form-control" name="status" required>
                    <option value="In Custody" <?= ($status=="In Custody")?"selected":"" ?>>In Custody</option>
                    <option value="Under Investigation" <?= ($status=="Under Investigation")?"selected":"" ?>>Under Investigation</option>
                    <option value="Released" <?= ($status=="Released")?"selected":"" ?>>Released</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Remarks</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="remarks" rows="3"><?= htmlspecialchars($remarks) ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            <div class="col-sm-3 d-grid">
                <a class="btn btn-outline-secondary" href="arrestees.php" role="button">Cancel</a>
            </div>
        </div>
    </form>

    <!-- Table of Arrestees -->
<h3>Arrestee Records</h3>
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
                    <a href="edit.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-success">Edit</a>
                    <a href="delete_arrestee.php?id=<?= urlencode($row['arrestee_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endwhile;
    else: ?>
        <tr><td colspan="10" class="text-center">No arrestees found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
