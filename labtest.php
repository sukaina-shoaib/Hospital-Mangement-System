<?php
session_start();
include("Database.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id']; // Get the logged-in patient's ID
$labtest_details = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["test_types"])) {
    $test_types = $_POST["test_types"]; // Array of selected test types
    $test_date = $_POST["test_date"];
    $test_time = $_POST["test_time"];

    $selected_tests = [];
    $total_cost = 0;
    $lab_ids = [];

    // Fetch descriptions and details for the selected tests
    foreach ($test_types as $test) {
        $test_query = "SELECT LabID, Name, description, Amount FROM Lab WHERE Name = '$test'";
        $result = mysqli_query($conn, $test_query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $selected_tests[] = [
                "name" => $row['Name'],
                "description" => $row['description'] ?? "No description available.",
                "amount" => $row['Amount']
            ];
            $lab_ids[] = [
                "id" => $row['LabID'],

            ];
            $total_cost += $row['Amount']; // Add test cost to total
        }
    }

    // Insert each selected test into the LabTest table
    foreach ($lab_ids as $lab) {
        $testno = "T" . time() . rand(1000, 9999) . $lab['id']; // Unique testno for each test
        $labtest_query = "INSERT INTO LabTest (testno, PatientID, Date, Time, Name, LabID) 
                          VALUES ('$testno', '$patient_id', '$test_date', '$test_time', 
                                  '" . implode(", ", $test_types) . "', '" . $lab['id'] . "')";
        if (!mysqli_query($conn, $labtest_query)) {
            echo "Error: " . mysqli_error($conn);
            exit;
        }
    }

    // Save the details for display
    $labtest_details = [
        "receipt_no" => $testno,
        "tests" => $selected_tests,
        "date" => $test_date,
        "time" => $test_time,
        "total_cost" => $total_cost
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Lab Test</title>
    <style>
  body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fa;
    color: #333;
    margin: 0;
    padding: 0;
    line-height: 1.6;
}

/* Wrapper for forms and details */
.form-wrapper, .labtest-details {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 50px auto;
    padding: 30px;
    border: 1px solid #e0e0e0;
}

/* Headings */
h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
    color: #4CAF50;
    font-weight: bold;
}

/* Form elements */
select, input[type="date"], input[type="time"] {
    width: 97.5%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    transition: border-color 0.3s;
}

select:focus, input[type="date"]:focus, input[type="time"]:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Button styles */
button {
    width: 102%;
    padding: 12px;
    margin: 10px 0;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #45a049;
}

/* Details section */
.details {
    margin: 10px 0;
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.details:last-child {
    border-bottom: none;
}

/* Test options */
.test-options {
    display: flex;
    flex-direction: column;
    margin: 15px 0;
}

.test-option {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.test-option input {
    margin-right: 10px;
}

/* Links */
a {
    display: inline-block;
    margin-top: 20px;
    text-align: center;
    color: #4CAF50;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

/* Responsive design */
@media (max-width: 600px) {
    .form-wrapper, .labtest-details {
        padding: 20px;
    }

    h2 {
        font-size: 24px;
    }

    button {
        font-size: 14px;
    }
}
    </style>
</head>
<body>
    <?php if ($labtest_details): ?>
        <div class="labtest-details">
            <h2>Your Lab Test Details</h2>
            <div class="details"><strong>Test Number:</strong> <?php echo $labtest_details['receipt_no']; ?></div>
            <div class="details"><strong>Date:</strong> <?php echo $labtest_details['date']; ?></div>
            <div class="details"><strong>Time:</strong> <?php echo $labtest_details['time']; ?></div>
            <div class="details"><strong>Selected Tests:</strong></div>
            <ul>
                <?php foreach ($labtest_details['tests'] as $test): ?>
                    <li><strong><?php echo $test['name']; ?>:</strong> <?php echo $test['description']; ?> - Rs <?php echo $test['amount']; ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="details"><strong>Total Cost:</strong> RS <?php echo $labtest_details['total_cost']; ?></div>
            <a href="index.php">Return to Main Page</a>
        </div>
    <?php else: ?>
        <div class="form-wrapper">
            <h2>Book a Lab Test</h2>
            <form method="POST" action="">
                <div class="test-options">
                    <div class="test-option">
                        <input type="checkbox" id="xray" name="test_types[]" value="X-ray">
                        <label for="xray">X-ray - A quick and painless imaging test.</label>
                    </div>
                    <div class="test-option">
                        <input type="checkbox" id="ctscan" name="test_types[]" value="CT Scan">
                        <label for="ctscan">CT Scan - Detailed body imaging using X-rays.</label>
                    </div>
                    <div class="test-option">
                        <input type="checkbox" id="ultrasound" name="test_types[]" value="Ultrasound">
                        <label for="ultrasound">Ultrasound - Imaging with sound waves.</label>
                    </div>
                    <div class="test-option">
                        <input type="checkbox" id="injection" name="test_types[]" value="Injection">
                        <label for="injection">Injection - Direct medicine administration.</label>
                    </div>
                </div>
                <label for="test_date">Select Date</label>
                <input type="date" id="test_date" name="test_date" required>
                <label for="test_time">Select Time</label>
                <input type="time" id="test_time" name="test_time" required>
                <button type="submit">Book Test</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
