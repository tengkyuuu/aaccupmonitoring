<?php
// Include your database connection file
include 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_campus'])) {
    // Retrieve form data
    $campusName = $_POST['campus_name'];
    $location = $_POST['campus_location'];
    $contactInfo = $_POST['contact_info'];

    // Insert data into the database
    $sql = "INSERT INTO universities (Name, Location, ContactInformation) VALUES ('$campusName', '$location', '$contactInfo')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect back to the campus page after successful addition
        header("Location: campus.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close database connection
$conn->close();
?>