<?php
session_start();
include("config.php");

// Check if the user is logged in
if(isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    // Query to fetch notifications for the user
    $query = "SELECT NotificationID, Message FROM notifications WHERE UserID = ? ORDER BY NotificationID DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output notifications as HTML
    while($row = $result->fetch_assoc()) {
        echo '<div class="notification-item">' . $row['Message'] . '</div>';
    }
    $stmt->close();
} else {
    // If the user is not logged in, return an error message or handle it based on your application logic
    echo 'User not logged in';
}
?>
