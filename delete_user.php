<?php
// Include the config.php file
include('config.php');

// Check if the user ID is set in the URL
if (isset($_GET['userID'])) {
    // Escape user ID to prevent SQL injection
    $userID = mysqli_real_escape_string($conn, $_GET['userID']);

    // SQL query to delete user
    $sql = "DELETE FROM users WHERE UserID = '$userID'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the page where users are listed
        header("Location: user.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // If user ID is not set, redirect back to the page where users are listed
    header("Location: user.php");
    exit();
}

// Close database connection
$conn->close();
?>