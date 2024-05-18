<?php
session_start();
include("config.php");

if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['UserID'];
$contactId = $_GET['userID'];

// Fetch user's full name
$query = "SELECT firstName, lastName FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $contactId);
$stmt->execute();
$stmt->bind_result($contactFirstName, $contactLastName);
$stmt->fetch();
$contactFullName = $contactFirstName . " " . $contactLastName;
$stmt->close();

// Fetch messages between the logged-in user and the selected contact
$query = "SELECT senderID, message, timestamp FROM messages 
          WHERE (senderID = ? AND receiverID = ?) OR (senderID = ? AND receiverID = ?)
          ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $userId, $contactId, $contactId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h2><?php echo $contactFullName; ?></h2>
<div class="chat-messages">
    <?php foreach ($messages as $message): ?>
        <div class="message <?php echo $message['senderID'] == $userId ? 'sent' : 'received'; ?>">
            <p><?php echo htmlspecialchars($message['message']); ?></p>
            <span class="timestamp"><?php echo date('Y-m-d H:i:s', strtotime($message['timestamp'])); ?></span>
        </div>
    <?php endforeach; ?>
</div>
<form onsubmit="sendMessage(event)">
    <input type="hidden" name="receiverID" value="<?php echo $contactId; ?>">
    <textarea name="message" placeholder="Type your message..." required></textarea>
    <button type="submit">Send</button>
</form>
