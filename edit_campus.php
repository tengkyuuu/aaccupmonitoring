<?php
// Include your database connection file
include 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'edit_campus') {
        // Retrieve form data
        $campusID = $_POST['campus_id'];
        $campusName = $_POST['campus_name'];
        $location = $_POST['campus_location'];
        $contactInfo = $_POST['contact_info'];

        // Perform update query
        $sql = "UPDATE universities SET Name='$campusName', Location='$location', ContactInformation='$contactInfo' WHERE UniversityID='$campusID'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect back to the campus page after successful edit
        header("Location: campus.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
}

// Close database connection
$conn->close();
?>