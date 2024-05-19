<?php
session_start(); // Start session

include("config.php");

// Check if the user is logged in before accessing session variables
if(isset($_SESSION['UserID'])) {
    // Fetch the user's last name and profile image from the database based on the user's ID stored in the session
    $userId = $_SESSION['UserID'];
    $query = "SELECT lastName, img FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($lastName, $profileImage);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle case where user is not logged in or session is not set
    // You can redirect the user to the login page or handle it based on your application logic
    // For now, let's set $lastName to an empty string and $profileImage to a default image path
    $lastName = "Loko na";
    $profileImage = "default-profile-image.jpg";
}

?>
<?php
include("config.php");

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['UserID'];

// Fetch the role of the current user
$queryRole = "SELECT Role FROM users WHERE UserID = ?";
$stmtRole = $conn->prepare($queryRole);
$stmtRole->bind_param('i', $userId);
$stmtRole->execute();
$resultRole = $stmtRole->get_result();
$currentUserRole = $resultRole->fetch_assoc()['Role'];
$stmtRole->close();

// Adjust the query based on the role
if ($currentUserRole === 'Administrator') {
    $query = "SELECT UserID, firstName, lastName FROM users WHERE Role IN ('Administrator', 'Faculty', 'Assessor') AND UserID != ?";
} elseif ($currentUserRole === 'Assessor') {
    $query = "SELECT UserID, firstName, lastName FROM users WHERE Role = 'Administrator' AND UserID != ?";
} else {
    $query = "SELECT UserID, firstName, lastName FROM users WHERE Role IN ('Administrator', 'Faculty') AND UserID != ?";
}

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Box</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <link rel="shortcut icon" href="images/logo coe.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        
        .main {
            width: 100%;
            background-color: grey;
        }
        .main-title {
            display: flex;
            margin: 20px;
            align-items: center;
        }
        .main-title h2 {
            margin-left: 10px;
        }
        .main-content {
            display: flex;
        }
              
        .directory {
            margin-left: 50px;
            position: relative;
            bottom: 20px;
            font-size: small;
        }
        .directory a {
            text-decoration: none;
            color: black;
        }
        .directory a:hover {
            color: orangered;
        }
        .users-list {
            width: 30%;
            border-right: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
        }
        .users-list a {
            display: block;
            padding: 10px;
            margin-bottom: 5px;
            text-decoration: none;
            color: #000;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .users-list a:hover {
            background-color: #f0f0f0;
        }
        .chat-box {
            width: 70%;
            padding: 20px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: scroll;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .message {
            margin: 10px 0;
        }
        .message.sent {
            text-align: right;
        }
        .message.received {
            text-align: left;
        }
        .message .timestamp {
            display: block;
            font-size: 0.8em;
            color: #888;
        }
        form {
            display: flex;
            margin-top: 20px;
        }
        textarea {
            flex: 1;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
        }
        button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            background: #007bff;
            color: #fff;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
<section class="heading">
<nav class="navbar">
        <img src="images/loginimg.png" alt="logo" class="logo">
        <div class="title">
            <a href="#"><h5>Jose Rizal Memorial State University</h5></a>
            <a href="#"><h2>AACCUP Accreditation Monitoring System</h2></a>
        </div>
        <div class="profile-dropdown">
            <div onclick="toggle()" class="profile-dropdown-btn">
                <div class="profile-img" style="background-image: url(<?php echo $profileImage; ?>);">
                    <i class="fa-solid fa-circle"></i>
                </div>
                <span>
                    <?php echo $lastName; ?>
                    <i class="fa-solid fa-angle-down"></i>
                </span>
            </div>
            <ul class="profile-dropdown-list">
                <li class="profile-dropdown-list-item">
                    <a href="edit_profile.php">
                        <i class="fa-regular fa-user"></i>
                        Edit Profile
                    </a>
                </li>
                <li class="profile-dropdown-list-item">
                    <a href="#">
                        <i class="fa-regular fa-sliders"></i>
                        Settings
                    </a>
                </li>
                <li class="profile-dropdown-list-item">
                    <a href="index.php">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Log Out
                    </a>
                </li>
            </ul>
        </div>

    </nav>
    </section>
    
    <section class="container">
    <nav class="side">
            <div class="sidebar">
            <div class="side-logo">
                    <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="profile-img-sidebar">
                    <?php
                    // Fetch the user's full name from the database based on UserID stored in the session
                    $userId = $_SESSION['UserID'];
                    $query = "SELECT firstName, lastName FROM users WHERE UserID = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $stmt->bind_result($firstName, $lastName);
                    $stmt->fetch();
                    $fullName = $firstName . " " . $lastName; // Concatenate first name and last name
                    $stmt->close();
                    ?>
                    <h1><?php echo $fullName; ?></h1>
                </div>
            <ul>
                    <li><a href="dashboardadmin.php">
                        <i class="fa-solid fa-gauge"></i>
                        <span class="nav-item">Dashboard</span>
                    </a>
                    </li>
                    <li><a href="user.php">
                        <i class="fa-solid fa-gear"></i>
                        <span class="nav-item">Users</span>
                    </a>
                    </li>
                    <li><a href="campus.php">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span class="nav-item">Campuses</span>
                    </a>
                    </li>
                    <li><a href="departments.php">
                        <i class="fa-solid fa-file"></i>
                        <span class="nav-item">Departments  </span>
                    </a>
                    </li>
                    <li><a href="program.php">
                        <i class="fa-solid fa-bell"></i>
                        <span class="nav-item">Programs</span>
                    </a>
                    </li>
                    <li><a href="visits.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Visits</span>
                    </a>
                    </li>
                    <li><a href="documents.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Documents</span>
                    </a>
                    </li>
                    <li><a href="tasks.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Tasks</span>
                    </a>
                    </li>
                    <li><a href="acommunication.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Communication</span>
                    </a>
                    </li>
                </ul>
        </div>
    </nav>
    <div class="main">
        <div class="main-title">
            <i class="fa-solid fa-gauge"></i>
            <h2>Communication</h2>
        </div>
        <div class="directory">
            <p><a href="dashboardadmin.php">Dashboard</a> > Communication</p>
        </div>
        <div class="main-content">
            <div class="users-list">
                <?php foreach ($users as $user): ?>
                    <a href="javascript:void(0)" onclick="loadChat(<?php echo $user['UserID']; ?>)">
                        <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="chat-box" id="chat-box">
                <p>Welcome to the AACCUP-Monitoring Communication System.</p>
            </div>
        </div>
    </div>
</section>

<script>
    function loadChat(userId) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'load_chat.php?userID=' + userId, true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('chat-box').innerHTML = this.responseText;
            }
        }
        xhr.send();
    }

    function sendMessage(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_message.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('chat-box').innerHTML = this.responseText;
            }
        }
        xhr.send(formData);
    }
</script>
<script src="script.js"></script>
</body>

</html>