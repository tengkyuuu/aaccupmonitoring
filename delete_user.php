<?php
// Include the config.php file
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["userID"])) {
    // Escape user input for security
    $userID = $conn->real_escape_string($_GET["userID"]);

    // SQL to delete user
    $sql = "DELETE FROM users WHERE UserID = '$userID'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to users page after successful deletion
        header("Location: user.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>