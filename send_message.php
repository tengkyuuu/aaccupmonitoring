<?php
session_start();
include("config.php");

if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $senderID = $_SESSION['UserID'];
    $receiverID = $_POST['receiverID'];
    $message = $_POST['message'];

    $query = "INSERT INTO messages (senderID, receiverID, message, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $senderID, $receiverID, $message);
    $stmt->execute();
    $stmt->close();

    // Reload the chat after sending a message
    header("Location: load_chat.php?userID=$receiverID");
    exit();
}
?>
