<?php
session_start();
include("Database.php");

// Ensure the user is logged in (if not, redirect to login page)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Handle appointment cancellation
if (isset($_POST['cancel_appointment'])) {
    $token_no = $_POST['token_no'];

    $cancel_query = "DELETE FROM Appointments WHERE token_no = '$token_no' AND PatientID = '$patient_id'";
    if (mysqli_query($conn, $cancel_query)) {
       
    } 
}

// Handle lab test cancellation
if (isset($_POST['cancel_labtest'])) {
    $testno = $_POST['testno'];

    $cancel_lab_query = "DELETE FROM LabTest WHERE testno = '$testno' AND PatientID = '$patient_id'";
    if (mysqli_query($conn, $cancel_lab_query)) {
        
    }
}

// Fetch the most recent appointment for the logged-in user
$appointment_query = "SELECT a.token_no, a.receipt_no, a.Time, a.payment, d.Name AS doctor_name 
                      FROM Appointments a
                      JOIN Doctor d ON a.DoctorID = d.DoctorID
                      WHERE a.PatientID = '$patient_id'
                      ORDER BY a.token_no DESC LIMIT 1"; // Use token_no to determine the latest record
$appointment_result = mysqli_query($conn, $appointment_query);

$recent_appointment = null;
if (mysqli_num_rows($appointment_result) > 0) {
    $recent_appointment = mysqli_fetch_assoc($appointment_result);
}

// Fetch the recent lab tests for the logged-in user
$test_query = "
    SELECT lt.testno, lt.Date, lt.Time, l.Name AS test_name, l.Amount AS test_amount
    FROM LabTest lt
    JOIN Lab l ON lt.LabID = l.LabID
    WHERE lt.PatientID = '$patient_id'
    ORDER BY lt.Date DESC, lt.Time DESC
";
$test_result = mysqli_query($conn, $test_query);

$labtest_details = [];
if (mysqli_num_rows($test_result) > 0) {
    while ($row = mysqli_fetch_assoc($test_result)) {
        $labtest_details[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <style>
        /* Global Styling */
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('images/inbg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background-color: rgba(0, 0, 0, 0.75);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .navbar div:first-child {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #f0a500;
        }

        .navbar img {
            width: 50px;
            height: 50px;
            margin-left: 15px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Header Styling */
        h1 {
            text-align: center;
            color: #fff;
            margin: 120px 0 20px;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* Card Styling */
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 25px;
            margin: 20px auto;
            max-width: 700px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            margin-bottom: 15px;
            font-size: 24px;
            color: #333;
            border-bottom: 2px solid #007BFF;
            display: inline-block;
            padding-bottom: 5px;
        }

        .card p {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
            margin: 8px 0;
        }

        /* Button Styling */
        .button {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            padding: 12px 18px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }

            .navbar a {
                margin: 5px 0;
            }

            .card {
                width: 90%;
                padding: 20px;
            }

            h1 {
                font-size: 28px;
                
            }
        }

    </style>
</head>
<body>
    <div class="navbar">
        <div>Hospital Management</div>
        <div>
            <a href="appointment.php">Appointment</a>
            <a href="labtest.php">Lab Test</a>
            <a href="generate_reports.php">Reports</a>
            <a href="patient_treatment.php">Treatment</a>
            <a href="logout.php">Logout</a>
            <?php if (isset($_SESSION['user_picture'])): ?>
                <img src="<?php echo $_SESSION['user_picture']; ?>" alt="Profile Picture">
            <?php endif; ?>
        </div>
    </div>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>

    <?php if (!empty($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php if ($recent_appointment): ?>
        <div class="card">
            <h2>Recent Appointment</h2>
            <p><strong>Doctor:</strong> <?php echo $recent_appointment['doctor_name']; ?></p>
            <p><strong>Token Number:</strong> <?php echo $recent_appointment['token_no']; ?></p>
            <p><strong>Receipt Number:</strong> <?php echo $recent_appointment['receipt_no']; ?></p>
            <p><strong>Time:</strong> <?php echo $recent_appointment['Time']; ?></p>
            <p><strong>Payment:</strong> RS <?php echo $recent_appointment['payment']; ?></p>
            <p><strong>Take a screenshot for future reference</strong></p>

            <form method="POST">
                <input type="hidden" name="token_no" value="<?php echo $recent_appointment['token_no']; ?>">
                <button type="submit" name="cancel_appointment" class="button">Cancel Appointment</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!empty($labtest_details)): ?>
        <div class="card">
            <h2>Your Recent Lab Tests</h2>
            <?php foreach ($labtest_details as $test): ?>
                <p><strong>Test Name:</strong> <?php echo $test['test_name']; ?></p>
                <p><strong>Test Number:</strong> <?php echo $test['testno']; ?></p>
                <p><strong>Date:</strong> <?php echo $test['Date']; ?></p>
                <p><strong>Time:</strong> <?php echo $test['Time']; ?></p>
                <p><strong>Amount:</strong> RS <?php echo $test['test_amount']; ?></p>

                <form method="POST">
                    <input type="hidden" name="testno" value="<?php echo $test['testno']; ?>">
                    <button type="submit" name="cancel_labtest" class="button">Cancel Lab Test</button>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
