<?php
session_start();
include("cp_connect.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    die("❌ Invalid request.");
}

$full_name = $age = $gender = $address = $arrest_date = $arrest_time = $offense_committed = $status = $remarks = "";
$errorMessage = $successMessage = "";

// ✅ Fetch record para ma-display sa form
$sql = "SELECT * FROM arrestees WHERE arrestee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Record not found.");
}

$row = $result->fetch_assoc();
$full_name         = $row['full_name'];
$age               = $row['age'];
$gender            = $row['gender'];
$address           = $row['address'];
$arrest_date       = $row['arrest_date'];
$arrest_time       = $row['arrest_time'];
$offense_committed = $row['offense_committed'];
$status            = $row['status'];
$remarks           = $row['remarks'];

// ✅ Kapag nag-submit ng form → update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name         = $_POST['full_name'] ?? '';
    $age               = $_POST['age'] ?? '';
    $gender            = $_POST['gender'] ?? '';
    $address           = $_POST['address'] ?? '';
    $arrest_date       = $_POST['arrest_date'] ?? '';
    $arrest_time       = $_POST['arrest_time'] ?? '';
    $offense_committed = $_POST['offense_committed'] ?? '';
    $status            = $_POST['status'] ?? '';
    $remarks           = $_POST['remarks'] ?? '';

    $sql = "UPDATE arrestees 
            SET full_name=?, age=?, gender=?, address=?, arrest_date=?, arrest_time=?, offense_committed=?, status=?, remarks=? 
            WHERE arrestee_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssssi", $full_name, $age, $gender, $address, $arrest_date, $arrest_time, $offense_committed, $status, $remarks, $id);

    if ($stmt->execute()) {
        $successMessage = "✅ Record updated successfully!";
        header("Location: arrested_record.php"); // balik sa list
        exit;
    } else {
        $errorMessage = "❌ Update failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Arrestee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container my-5">
    <h2>Edit Arrestee</h2>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" class="form-control" name="age" value="<?= htmlspecialchars($age) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select class="form-control" name="gender" required>
                <option value="Male" <?= ($gender=="Male")?"selected":"" ?>>Male</option>
                <option value="Female" <?= ($gender=="Female")?"selected":"" ?>>Female</option>
                <option value="Other" <?= ($gender=="Other")?"selected":"" ?>>Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($address) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Arrest Date</label>
            <input type="date" class="form-control" name="arrest_date" value="<?= htmlspecialchars($arrest_date) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Arrest Time</label>
            <input type="time" class="form-control" name="arrest_time" value="<?= htmlspecialchars($arrest_time) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Offense</label>
            <input type="text" class="form-control" name="offense_committed" value="<?= htmlspecialchars($offense_committed) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="In Custody" <?= ($status=="In Custody")?"selected":"" ?>>In Custody</option>
                <option value="Under Investigation" <?= ($status=="Under Investigation")?"selected":"" ?>>Under Investigation</option>
                <option value="Released" <?= ($status=="Released")?"selected":"" ?>>Released</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="remarks"><?= htmlspecialchars($remarks) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="arreted_records.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
