<?php
session_start();
include("config.php");

// Check if the user is logged in before accessing session variables
if (!isset($_SESSION['UserID'])) {
    // Redirect the user to the login page if not logged in
    header("Location: index.php");
    exit();
}

// Fetch the user's last name from the database based on the user's ID stored in the session
$userId = $_SESSION['UserID'];
$query = "SELECT lastName FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($lastName);
$stmt->fetch();
$stmt->close();

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $documentName = $_POST['document_name'];
    $uploadedBy = $_SESSION['UserID']; // Get the current user's ID

    // File upload handling
    $targetDirectory = "uploads/"; // Directory where files will be uploaded
    $targetFile = $targetDirectory . basename($_FILES["document"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (optional)
    if ($_FILES["document"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only specific file formats (you can modify as needed)
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // If everything is OK, try to upload file
        if (move_uploaded_file($_FILES["document"]["tmp_name"], $targetFile)) {
            // Insert document details into the database
            $query = "INSERT INTO documents (DocumentName, UploadedBy, UploadDate, FileURL) VALUES (?, ?, NOW(), ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sis", $documentName, $uploadedBy, $targetFile); // Bind the current user's ID
            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars(basename($_FILES["document"]["name"])) . " has been uploaded.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .main-flex {
            display: flex;
            justify-content: space-between;
        }
        .main-title h2 {
            margin-left: 10px;
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
        table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 15px;
            min-width: 100%;
            overflow: hidden;
            border-radius: 5px 5px 0 0;
        }  
        table thead tr {
            color: #fff;
            background: brown;
            text-align: left;
            font-weight: bold;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        tbody tr{
            border-bottom: 1px solid #ddd;
        }
        tbody tr:nth-of-type(odd){
            background: #f3f3f3;
        }
        tbody tr:last-of-type{
            border-bottom: 2px solid brown;
        }
        .button-bar {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            margin-bottom: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .main-content {
            margin: 5%;
            background-color: whitesmoke;
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 15px 25px rgba(0, 0.1, 0.9);
        }
        .abouts {
            display: flex;
            justify-content: space-between;
            margin: 5%;
        }  
        .main-item {
            margin-left: 10px;
            margin-top: 50px;
            padding: 30px;
            width: 30%;
            color: aliceblue;
            justify-content: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .main-item:last-child {
            margin-right: 10px;
        }
        .main-item i {
            font-size: 70px;
            position: relative;
            left: 10px;
        }
        .main-info h2 {
            font-size: 50px;
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
                    <div class="profile-img">
                        <i class="fa-solid fa-circle"></i>
                    </div>
                    <span>
                        <?php echo $lastName; ?>
                        <i class="fa-solid fa-angle-down"></i>
                    </span>
                </div>
                <ul class="profile-dropdown-list">
                    <li class="profile-dropdown-list-item">
                        <a href="#">
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
                    <img src="images/avatar.jpg">
                    <?php
                    // Fetch the user's full name from the database based on UserID stored in the session
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
                    <li><a href="dashboardfaculty.php">
                            <i class="fa-solid fa-gauge"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                    </li>
                    <li><a href="fdocuments.php">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span class="nav-item">Documents</span>
                        </a>
                    </li>
                    <li><a href="ftasks.php">
                            <i class="fa-solid fa-file"></i>
                            <span class="nav-item">Tasks</span>
                        </a>
                    </li>
                    <li><a href="fcommunication.php">
                            <i class="fa-solid fa-bell"></i>
                            <span class="nav-item">Communication</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main">
            <div class="main-flex">
                <div class="main-title">
                    <i class="fa-solid fa-gauge"></i>
                    <h2>Faculty Dashboard</h2>
                </div>
                <div class="main-title">
                    <i class="fa-solid fa-bell" style="margin-right: 50px; font-size:x-large"></i>
                </div>
            </div>
            <div class="directory">
                <p><a href="dashboarduser.php">Dashboard</a> > Faculty Dashboard</p>
            </div>

            <div class="main-content">
                <h2>Upload Document</h2>
                <form action="fdocuments.php" method="post" enctype="multipart/form-data">
                    <label for="document">Select Document:</label>
                    <input type="file" name="document" id="document" required><br><br>
                    <label for="document_name">Document Name:</label>
                    <input type="text" name="document_name" id="document_name" required><br><br>
                    <button type="submit" name="submit">Upload</button>
                </form>
            </div>
        </div>
    </section>
</body>

<script src="script.js"></script>
</html>