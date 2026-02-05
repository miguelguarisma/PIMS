<?php
session_start();
include("cp_connect.php");

if (!isset($_GET['id'])) {
    die("❌ Missing case ID.");
}

$case_id = $_GET['id'];
$errorMessage = $successMessage = "";

// Fetch existing case data
$sql = "SELECT * FROM cases WHERE case_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $case_id);
$stmt->execute();
$result = $stmt->get_result();
$case = $result->fetch_assoc();

if (!$case) {
    die("❌ Case not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $CaseNumber = $_POST['CaseNumber'] ?? '';
    $CaseType = $_POST['CaseType'] ?? '';
    $Municipality = $_POST['Municipality'] ?? '';
    $Barangay = $_POST['Barangay'] ?? '';
    $FiledDate = $_POST['FiledDate'] ?? '';
    $ClosedDate = $_POST['ClosedDate'] ?? null;
    $Status = $_POST['Status'] ?? '';
    $InvestigatingOfficerID = $_POST['InvestigatingOfficerID'] ?? '';
    $Complainant = $_POST['Complainant'] ?? '';
    $Respondent = $_POST['Respondent'] ?? '';
    $Notes = $_POST['Notes'] ?? '';

    $sql = "UPDATE cases 
            SET CaseNumber=?, CaseType=?, Municipality=?, Barangay=?, FiledDate=?, ClosedDate=?, Status=?, InvestigatingOfficerID=?, Complainant=?, Respondent=?, Notes=? 
            WHERE case_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $CaseNumber, $CaseType, $Municipality, $Barangay, $FiledDate, $ClosedDate, $Status, $InvestigatingOfficerID, $Complainant, $Respondent, $Notes, $CaseID);

    if ($stmt->execute()) {
        header("Location: case_record.php");
        exit;
    } else {
        $errorMessage = "❌ Failed to update case: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container my-5">
    <h2>Edit Case</h2>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Case Number</label>
            <input type="text" class="form-control" name="CaseNumber" value="<?= $case['CaseNumber'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Case Type</label>
            <input type="text" class="form-control" name="CaseType" value="<?= $case['CaseType'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Municipality</label>
            <input type="text" class="form-control" name="Municipality" value="<?= $case['Municipality'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Barangay</label>
            <input type="text" class="form-control" name="Barangay" value="<?= $case['Barangay'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Filed Date</label>
            <input type="date" class="form-control" name="FiledDate" value="<?= $case['FiledDate'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Closed Date</label>
            <input type="date" class="form-control" name="ClosedDate" value="<?= $case['ClosedDate'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="Status" required>
                <option value="Open" <?= $case['Status'] == "Open" ? "selected" : "" ?>>Open</option>
                <option value="Under Investigation" <?= $case['Status'] == "Under Investigation" ? "selected" : "" ?>>Under Investigation</option>
                <option value="Closed" <?= $case['Status'] == "Closed" ? "selected" : "" ?>>Closed</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Investigating Officer ID</label>
            <input type="text" class="form-control" name="InvestigatingOfficerID" value="<?= $case['InvestigatingOfficerID'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Complainant</label>
            <input type="text" class="form-control" name="Complainant" value="<?= $case['Complainant'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Respondent</label>
            <input type="text" class="form-control" name="Respondent" value="<?= $case['Respondent'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="Notes"><?= $case['Notes'] ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Case</button>
        <a href="case_record.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
