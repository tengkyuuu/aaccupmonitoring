<?php
// Include your database connection file
include 'config.php';

// Check if campus ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // SQL query to delete campus from the database
    $sql = "DELETE FROM universities WHERE UniversityID='$id'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect back to the campus page after successful deletion
        header("Location: campus.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>
