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
    <title>Doctor Appointments</title>
    <style>
       body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('images/inbg.jpg'); /* Replace with your image path */
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;

    color: #333;
}

.navbar {
    background-color: rgba(51, 51, 51, 0.8); /* Semi-transparent background */
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    padding: 10px 20px;
}

.navbar a {
    color: white;
    text-decoration: none;
    padding: 14px 20px;
    display: inline-block;
    transition: background-color 0.3s;
}

.navbar a:hover {
    background-color: rgba(255, 255, 255, 0.3); /* Light hover effect */
    color: black;
}

h2 {
    text-align: center;
    margin: 20px;
    color: #4CAF50; /* Green color for headings */
}

.content {
    
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.9); /* White background with transparency */
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
}

table {
   
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table th, table td {
    padding: 12px;
    border: 1px solid #ccc;
    text-align: left;
}

table th {
    background-color: #f4f4f4;
    color: #333;
}

table tr:nth-child(even) {
    background-color: #f9f9f9; /* Zebra striping for better readability */
}

table tr:hover {
    background-color: #f1f1f1; /* Highlight row on hover */
}

table td {
    color: #555; /* Slightly darker text for table cells */
}

/* Responsive design */
@media (max-width: 600px) {
    .navbar {
        flex-direction: column;
        align-items: center;
    }

    .navbar a {
        padding: 10px;
        width: 100%;
        text-align: center;
    }

    h2 {
        font-size: 24px;
    }

    table th, table td {
        padding: 8px;
    }
}
    </style>
</head>
<body>
    <div class="navbar">
        <a href="rhemutologist_treatment.php">Treatment</a>
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
