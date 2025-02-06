<?php
session_start();
include("Database.php");

// Check if the doctor is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['user_id']; // Doctor's ID from session
$doctor_name = $_SESSION['user_name']; // Doctor's name from session

// Fetch today's appointments for the logged-in doctor
$appointment_query = "
    SELECT 
        appointments.token_no,
        appointments.receipt_no,
        appointments.time AS appointment_time,
        appointments.payment,
        appointments.appointment_date,
        patient.Name AS patient_name,
        patient.PatientID
    FROM 
        appointments
    JOIN 
        patient ON appointments.PatientID = patient.PatientID
    WHERE 
        appointments.DoctorID = '$doctor_id' 
        AND appointments.appointment_date = CURDATE()
    ORDER BY 
        appointments.time ASC";

$result = mysqli_query($conn, $appointment_query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Appointments</title>
    <style>
          body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('images/inbg.jpg'); /* Replace with your image path */
    background-size: cover;
    background-repeat: no-repeat;
    /* background-position: center; */

    color: #333;
}

        .overlay {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 1200px;
            margin: 30px auto;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            font-size: 22px;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            font-size: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
        }

        .no-appointments {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 20px;
            
        }
        p{
            margin-left: 32px;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="physiotherapist_treatment.php">Treatment</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Today's Appointments for <?php echo htmlspecialchars($doctor_name); ?></h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Token No</th>
                        <th>Receipt No</th>
                        <th>Patient Name</th>
                        <th>Patient ID</th>
                        <th>Appointment Date</th>
                        <th>Time</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['token_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['receipt_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['PatientID']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found for today.</p>
        <?php endif; ?>
    </div>
</body>
</html>
