<?php
include("database.php");

$patientID = null;
$name = $email = $cnic = "";
$profilePicturePath = "";

// Check if the upload directory exists and is writable
$uploadDirectory = "uploads/";
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);  // Create the directory if it doesn't exist
    echo "Upload directory created.<br>";
} else {
    if (!is_writable($uploadDirectory)) {
        echo "The upload directory is not writable.<br>";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $cnic = $_POST['cnic'];
    $date_of_birth = $_POST['date_of_birth']; // Capture Date of Birth
    $gender = $_POST['gender'];
    $password = $_POST['password'];

    // Handle the file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file extension (only allow image files)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension; // Unique file name
            $uploadPath = $uploadDirectory . $newFileName;

            // Move the file to the desired directory
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $profilePicturePath = $uploadPath;
            } else {
                echo "Error uploading the file.<br>";
                exit;
            }
        } else {
            echo "Invalid file type. Only image files are allowed.<br>";
            exit;
        }
    } else {
        echo "No file uploaded or error with file.<br>";
        var_dump($_FILES); // This will give you more information about the error
        exit;
    }

    // Insert data into the Patient table
    $query = "INSERT INTO Patient (Name, Email, PhoneNumber, CNIC, DateOfBirth, Gender, Password, PicturePath) 
              VALUES ('$name', '$email', '$phone', '$cnic', '$date_of_birth', '$gender', '$password', '$profilePicturePath')";

    if (mysqli_query($conn, $query)) {
        // Fetch PatientID based on the CNIC
        $fetchQuery = "SELECT PatientID FROM Patient WHERE CNIC = '$cnic'";
        $result = mysqli_query($conn, $fetchQuery);
        $patient = mysqli_fetch_assoc($result);
        $patientID = $patient['PatientID'];
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            background-image: url('images/bgl.jpg'); 
            background-size: cover; /* Make sure the image covers the entire page */
            background-position: center; /* Center the image */
            background-attachment: fixed; /* Keep the image fixed during scroll */
            margin: 0;
            padding: 0;
            /* color: black; */
            font-family: Arial, sans-serif;
        }

        .form-wrapper {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent white background */
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            color: white;
            background-color: rgba(0, 0, 0, 0.6); /* Slightly more opaque for card */
        }

        h2, p {
            text-align: center;
        }
        p a{
            text-decoration: none;
            color: white;
            
        }

        input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="date"], input[type="file"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($patientID)): ?>
            <div class="card">
                <h2>Signup Successful!</h2>
                <p><strong>Patient ID:</strong> <?= htmlspecialchars($patientID) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                <p><strong>CNIC:</strong> <?= htmlspecialchars($cnic) ?></p>
                <p>Please save this information for future reference.</p>
                <!-- Button or Link to go to the login page -->
                <p><a href="login.php">Go to Login Page</a></p>
            </div>
        <?php else: ?>
            <h2>Sign Up</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Enter your name" required><br>
                <input type="email" name="email" placeholder="Enter your email" required><br>
                Date of birth: <input type="date" name="date_of_birth" required><br>
                <input type="tel" name="phone" placeholder="Enter your phone number" required><br>
                <input type="text" name="cnic" placeholder="Enter your CNIC" required><br>
                <div>
                    <label>Gender:</label>
                    <input type="radio" name="gender" value="Male" required> Male
                    <input type="radio" name="gender" value="Female" required> Female
                </div>
                <input type="password" name="password" placeholder="Enter your password" required><br>
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" name="profile_picture" accept="image/*" required><br>

                <button type="submit">Sign Up</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
