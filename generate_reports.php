<?php 
session_start();
include("Database.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id']; // Get the logged-in patient's ID

// Query to fetch lab test reports that are at least 1 day old
$query = "
    SELECT lt.TestNo, lt.Name AS TestName, l.Name AS LabName, l.LabID, lt.Date
    FROM labtest lt
    JOIN lab l ON lt.LabID = l.LabID
    WHERE lt.PatientID = ? 
    AND DATE(lt.Date) <= DATE(DATE_ADD(NOW(), INTERVAL -1 DAY))
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
        .report-details { margin: 10px 0; padding: 10px; border-bottom: 1px solid #ddd; }
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
                    <strong>Test Number:</strong> <?php echo htmlspecialchars($report['TestNo']); ?><br>
                    <strong>Test Name:</strong> <?php echo htmlspecialchars($report['TestName']); ?><br>
                    <strong>Lab Name:</strong> <?php echo htmlspecialchars($report['LabName']); ?><br>
                    <strong>Test Date:</strong> <?php echo htmlspecialchars($report['Date']); ?><br>
                    <strong>Report:-</strong> 
                    
                    <div class="image-container">
                        <?php if ($report['LabID'] == 1): ?>
                            <img src="images/xray.jpg" alt="X-Ray Report">
                        <?php elseif ($report['LabID'] == 2): ?>
                            <img src="images/CTscan.jpg" alt="CT Scan Report">
                        <?php elseif ($report['LabID'] == 3): ?>
                            <img src="images/ultrasound.jpg" alt="Ultrasound Report">
                        <?php else: ?>
                            <p>No image available for this test.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
