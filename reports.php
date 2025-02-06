<?php
session_start();
include("Database.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id']; // Get the logged-in patient's ID

// Query to fetch lab test reports
$query = "
    SELECT lt.TestNo, lt.Name AS TestName, l.Name AS LabName, l.report, l.LabID, lt.Date
    FROM labtest lt
    JOIN lab l ON lt.LabID = l.LabID
    WHERE lt.PatientID = ? 
    AND lt.report IS NOT NULL
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Test Reports</title>
    <style>
        .report-container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        h2 { text-align: center; }
        .report-details { margin: 10px 0; }
        .report-link { color: blue; text-decoration: underline; }
        .image-container img { max-width: 100%; }
    </style>
</head>
<body>
    <div class="report-container">
        <h2>Your Lab Test Reports</h2>
        <?php if (empty($reports)): ?>
            <p>No reports available for tests.</p>
        <?php else: ?>
            <?php foreach ($reports as $report): ?>
                <div class="report-details">
                    <strong>Test Number:</strong> <?php echo $report['TestNo']; ?><br>
                    <strong>Test Name:</strong> <?php echo $report['TestName']; ?><br>
                    <strong>Lab Name:</strong> <?php echo $report['LabName']; ?><br>
                    <strong>Test Date:</strong> <?php echo $report['Date']; ?><br>
                    <strong>Report Link:</strong> 
                    
                    <?php if ($report['LabID'] == 1): ?>
                        <!-- Display image1 if LabID is 1 -->
                        <div class="image-container">
                            <img src="images/xray.jpg" alt="Lab Report Image 1">
                        </div>
                    <?php elseif ($report['LabID'] == 2): ?>
                        <!-- Display image2 if LabID is 2 -->
                        <div class="image-container">
                            <img src="images/CTscan.jpg" alt="Lab Report Image 2">
                        </div>
                    <?php elseif ($report['LabID'] == 3): ?>
                        <!-- Display image3 if LabID is 3 -->
                        <div class="image-container">
                            <img src="images/ultrasound.jpg" alt="Lab Report Image 3">
                        </div>
                    <?php else: ?>
                        <!-- If LabID doesn't match 1, 2, or 3, show a generic link -->
                        <a href="<?php echo $report['report']; ?>" target="_blank" class="report-link">View Report</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
