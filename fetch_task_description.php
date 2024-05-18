<?php
// Include config.php for database connection
include("config.php");

// Check if taskID is set and valid
if(isset($_GET['taskID'])) {
    // Fetch the full task description from the database based on the task ID
    $taskID = $_GET['taskID'];
    $query = "SELECT Description FROM tasks WHERE TaskID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $taskID);
    $stmt->execute();
    $stmt->bind_result($description);
    $stmt->fetch();
    echo $description;
    $stmt->close();
} else {
    echo "Task description not found.";
}
?>