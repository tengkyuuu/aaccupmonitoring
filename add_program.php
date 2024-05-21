<?php
include("config.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $code = $_POST["code"];
    $program_name = $_POST["program_name"];
    $department_id = $_POST["department"];
    
    // Set default values for level, accreditation status, and accreditation level
    $level = "N/A";
    $accreditation_status = "Candidate";
    $accreditation_level = null;

    // Prepare and bind parameters for the INSERT statement
    $stmt = $conn->prepare("INSERT INTO programs (Code, Name, DepartmentID, Level, AccreditationStatus, AccreditationLevel) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $code, $program_name, $department_id, $level, $accreditation_status, $accreditation_level);

    // Execute the statement
    if ($stmt->execute()) {
        // Program added successfully, redirect to the previous page or wherever you want
        header("Location: program.php");
        exit();
    } else {
        // Error occurred, handle as needed
        echo "Error: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the form page if accessed directly without form submission
    header("Location: program.php");
    exit();
}
?>
