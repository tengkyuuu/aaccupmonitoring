<?php
session_start();
include("config.php");

// Check if the user is logged in
if(isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    // Query to check if there are new notifications for the user
    $query = "SELECT COUNT(*) AS newNotifications FROM notifications WHERE UserID = ? AND Status = 'New'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $newNotifications = $row['newNotifications'];

    // Return true if there are new notifications, false otherwise
    if ($newNotifications > 0) {
        echo 'true';
    } else {
        echo 'false';
    }
    $stmt->close();
} else {
    // If the user is not logged in, return an error message or handle it based on your application logic
    echo 'User not logged in';
}
?>
