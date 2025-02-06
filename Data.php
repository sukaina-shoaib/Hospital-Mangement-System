<?php
// Database connection details
$servername = "localhost"; // Change if needed
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create Database
$sql = "CREATE DATABASE IF NOT EXISTS hospital";
if ($conn->query($sql) === false) {
    echo "Database created successfully<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the Database
$conn->select_db("hospital");

// Create Appointments Table
$sql = "CREATE TABLE IF NOT EXISTS appointments (
    token_no INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(20) NOT NULL,
    DoctorId VARCHAR(10) NOT NULL,
    PatientId VARCHAR(10) NOT NULL,
    time VARCHAR(25) NOT NULL,
    payment DECIMAL(10,2) NOT NULL,
    appointment_date DATE NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'appointments': " . $conn->error);
}

// Create Doctor Table
$sql = "CREATE TABLE IF NOT EXISTS doctor (
    DoctorID VARCHAR(5) PRIMARY KEY,
    Name VARCHAR(30) NOT NULL,
    Field VARCHAR(30) NOT NULL,
    PhoneNumber INT(11) NOT NULL,
    Password VARCHAR(14) NOT NULL,
    Day VARCHAR(20) NOT NULL,
    Time VARCHAR(30) NOT NULL,
    amount DECIMAL(10,2) NOT NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'doctor': " . $conn->error);
}

// Create Exercise Details Table
$sql = "CREATE TABLE IF NOT EXISTS exercise_details (
    e_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    exercise VARCHAR(255) NOT NULL,
    descriptions TEXT NULL,
    instructions TEXT NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'exercise_details': " . $conn->error);
}

// Create Lab Table
$sql = "CREATE TABLE IF NOT EXISTS lab (
    LabID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    instructions TEXT NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'lab': " . $conn->error);
}

// Create Lab Test Table
$sql = "CREATE TABLE IF NOT EXISTS labtest (
    TestNo VARCHAR(15) PRIMARY KEY,
    PatientID VARCHAR(5) NOT NULL,
    Date DATE NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Time TIME NOT NULL,
    LabID INT(11) NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'labtest': " . $conn->error);
}

// Create Patient Table
$sql = "CREATE TABLE IF NOT EXISTS patient (
    PatientID VARCHAR(5) PRIMARY KEY,
    Name VARCHAR(25) NOT NULL,
    Email VARCHAR(30) NOT NULL,
    PhoneNumber INT(11) NOT NULL,
    CNIC VARCHAR(15) NOT NULL,
    DateOfBirth DATE NOT NULL,
    Gender ENUM('Male', 'Female') NOT NULL,
    Password VARCHAR(15) NOT NULL,
    PicturePath VARCHAR(255) NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'patient': " . $conn->error);
}

// Create Physiotherapy Table
$sql = "CREATE TABLE IF NOT EXISTS physiotherapy (
    p_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    DoctorID VARCHAR(5) NOT NULL,
    PatientID VARCHAR(5) NOT NULL,
    exercise TEXT NULL,
    instruction TEXT NULL,
    followup_date DATE NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'physiotherapy': " . $conn->error);
}

// Create Rheumatologist Table
$sql = "CREATE TABLE IF NOT EXISTS rheumatologist (
    treatmentid INT(11) AUTO_INCREMENT PRIMARY KEY,
    DoctorID VARCHAR(5) NULL,
    PatientID VARCHAR(5) NULL,
    disease VARCHAR(255) NULL,
    history TEXT NULL,
    symptom TEXT NULL,
    followup_date DATE NULL,
    medicine VARCHAR(255) NULL,
    dosage VARCHAR(255) NULL,
    m_instruction TEXT NULL,
    test VARCHAR(255) NULL,
    t_instructions TEXT NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'rheumatologist': " . $conn->error);
}

// Create Treatment Table
$sql = "CREATE TABLE IF NOT EXISTS treatment (
    treatment_no INT(11) AUTO_INCREMENT PRIMARY KEY,
    symptom VARCHAR(255) NULL,
    medicine VARCHAR(255) NULL,
    m_dosage VARCHAR(255) NULL,
    m_instructions TEXT NULL,
    diseases VARCHAR(255) NULL
)";
if ($conn->query($sql) === false) {
    die("Error creating table 'treatment': " . $conn->error);
}

// Close connection
$conn->close();
?>
