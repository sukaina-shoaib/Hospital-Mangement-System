<?php 
session_start();
include("Database.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Fetch the list of doctors from the database
$doctor_query = "SELECT DoctorID, name, Field FROM doctor";
$doctor_result = mysqli_query($conn, $doctor_query);

// Check if a doctor is selected and fetch treatment history
if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];

    // Fetch the doctor's field to determine which table to query
    $field_query = "SELECT Field FROM doctor WHERE DoctorID = ?";
    $field_stmt = mysqli_prepare($conn, $field_query);
    mysqli_stmt_bind_param($field_stmt, "i", $doctor_id);
    mysqli_stmt_execute($field_stmt);
    $field_result = mysqli_stmt_get_result($field_stmt);
    $doctor_field = mysqli_fetch_assoc($field_result)['Field'];

    // Prepare the appropriate query based on the doctor's field
    if ($doctor_field === 'Rhemutologist') {
        $query = "SELECT * FROM rheumatologist WHERE DoctorID = ? AND PatientID = ?";
    } elseif ($doctor_field === 'Physiotherapist') {
        $query = "SELECT * FROM physiotherapy WHERE DoctorID = ? AND PatientID = ?";
    } else {
        echo json_encode(["error" => "No treatment history found for the selected doctor."]);
        exit;
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $doctor_id, $patient_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Prepare the response
    $response = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($doctor_field === 'Rhemutologist') {
                $response[] = [
                    "treatmentid" => $row['treatmentid'],
                    "disease" => $row['disease'],
                    "history" => $row['history'],
                    "symptom" => $row['symptom'],
                    "followup_date" => $row['followup_date'],
                    "medicine" => $row['medicine'],
                    "dosage" => $row['dosage'],
                    "m_instruction" => $row['m_instruction'],
                    "test" => $row['test'],
                    "t_instructions" => $row['t_instructions']
                ];
            } elseif ($doctor_field === 'Physiotherapist') {
                $response[] = [
                    "p_id" => $row['p_id'],
                    "exercise" => $row['exercise'],
                    "instruction" => $row['instruction'],
                    "followup_date" => $row['followup_date']
                ];
            }
        }
    } else {
        $response = ["error" => "No treatment history found for the selected doctor."];
    }

    // Ensure proper JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Treatment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .form-container {
            width: 50%;
            max-width: 600px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h2 {
            text-align: left;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .form-group {
            margin-top: 10px;
        }

        .form-control {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .history-container {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Treatment</h2>
    
    <!-- Doctor Selection Dropdown -->
    <div class="form-group">
        <label for="doctor_id">Select Doctor:</label>
        <select id="doctor_id" name="doctor_id" class="form-control" required>
            <option value="" disabled selected>Select a Doctor</option>
            <?php while ($doctor = mysqli_fetch_assoc($doctor_result)): ?>
                <option value="<?php echo htmlspecialchars($doctor['DoctorID']); ?>">
                    <?php echo htmlspecialchars($doctor['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Treatment History Container -->
    <div class="history-container" id="history-container" style="display:none;">
        <h4>Patient Treatment History</h4>
        <div id="history-details"></div>
    </div>

</div>

<script>
    $(document).ready(function() {
        $('#doctor_id').change(function() {
            var doctorID = $(this).val();

            if (doctorID) {
                $.ajax({
                    type: 'GET',
                    url: window.location.href, // Use the current PHP file
                    data: { doctor_id: doctorID },
                    dataType: 'json',
                    success: function(response) {
                        $('#history-container').show();
                        var historyHtml = '';

                        if (response.error) {
                            historyHtml = `<p>${response.error}</p>`;
                        } else {
                            response.forEach(function(item) {
                                if (item.treatmentid) { // Rheumatologist
                                    historyHtml += 
                                        // <p><strong>Treatment ID:</strong> ${item.treatmentid}</p>
                                        `<p><strong>Disease:</strong> ${item.disease}</p>
                                        <p><strong>History:</strong> ${item.history}</p>
                                        <p><strong>Symptom:</strong> ${item.symptom}</p>
                                        <p><strong>Follow-up Date:</strong> ${item.followup_date}</p>
                                        <p><strong>Medicine:</strong> ${item.medicine}</p>
                                        <p><strong>Dosage:</strong> ${item.dosage}</p>
                                        <p><strong>Medicine Instructions:</strong> ${item.m_instruction}</p>
                                        <p><strong>Test:</strong> ${item.test}</p>
                                        <p><strong>Test Instructions:</strong> ${item.t_instructions}</p>`;
                                } else if (item.p_id) { // Physiotherapist
                                    historyHtml += 
                                        // `<p><strong>Physiotherapy ID:</strong> ${item.p_id}</p>
                                        `<p><strong>Exercise:</strong> ${item.exercise}</p>
                                        <p><strong>Instruction:</strong> ${item.instruction}</p>
                                        <p><strong>Follow-up Date:</strong> ${item.followup_date}</p>`;
                                }
                            });
                        }

                        $('#history-details').html(historyHtml);
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR:", xhr);
                        console.log("Status:", status);
                        console.log("Error:", error);
                        $('#history-container').show();
                        $('#history-details').html("<p>Error fetching data: " + error + "</p>");
                    }
                });
            } else {
                $('#history-container').hide();
            }
        });
    });
</script>

</body>
</html>
