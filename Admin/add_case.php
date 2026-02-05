<?php
session_start();
include("cp_connect.php");

$errorMessage = $successMessage = "";

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

    $sql = "INSERT INTO cases 
            (CaseNumber, CaseType, Municipality, Barangay, FiledDate, ClosedDate, Status, InvestigatingOfficerID, Complainant, Respondent, Notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $CaseNumber, $CaseType, $Municipality, $Barangay, $FiledDate, $ClosedDate, $Status, $InvestigatingOfficerID, $Complainant, $Respondent, $Notes);

    if ($stmt->execute()) {
        header("Location: case_record.php");
        exit;
    } else {
        $errorMessage = "❌ Failed to add case: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Case</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container my-5">
    <h2>Add New Case</h2>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Case Number</label>
            <input type="text" class="form-control" name="CaseNumber" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Case Type</label>
            <input type="text" class="form-control" name="CaseType" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Municipality</label>
            <input type="text" class="form-control" name="Municipality" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Barangay</label>
            <input type="text" class="form-control" name="Barangay" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Filed Date</label>
            <input type="date" class="form-control" name="FiledDate" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Closed Date</label>
            <input type="date" class="form-control" name="ClosedDate">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-control" name="Status" required>
                <option value="Open">Open</option>
                <option value="Under Investigation">Under Investigation</option>
                <option value="Closed">Closed</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Investigating Officer ID</label>
            <input type="text" class="form-control" name="InvestigatingOfficerID" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Complainant</label>
            <input type="text" class="form-control" name="Complainant" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Respondent</label>
            <input type="text" class="form-control" name="Respondent" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="Notes"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Add Case</button>
        <a href="case_record.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
