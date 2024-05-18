<?php
// Include the configuration file to establish a database connection
include("config.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve task ID and new status from the submitted form
    $taskID = $_POST['taskID'];
    $newStatus = $_POST['status'];

    // Prepare and execute an SQL query to update the task status in the database
    $query = "UPDATE tasks SET Status = ? WHERE TaskID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $newStatus, $taskID);

    if ($stmt->execute()) {
        // Redirect the user back to the ftasks.php page after the update is complete
        header("Location: ftasks.php");
        exit(); // Ensure that no further code is executed after redirection
    } else {
        echo "Error updating task status: " . $conn->error;
    }

    // Close the database connection and the prepared statement
    $stmt->close();
    $conn->close();
} else {
    // Handle case where form is not submitted
    echo "Form submission error: Method not POST.";
}
?>