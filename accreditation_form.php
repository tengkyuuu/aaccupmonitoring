<?php
session_start(); // Start session

include("config.php");

// Check if the user is logged in before accessing session variables
if(isset($_SESSION['UserID'])) {
    // Fetch the user's last name from the database based on the user's ID stored in the session
    $userId = $_SESSION['UserID'];
    $query = "SELECT lastName FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($lastName);
    $stmt->fetch();
    $stmt->close();
} else {
    // Handle case where user is not logged in or session is not set
    // You can redirect the user to the login page or handle it based on your application logic
    // For now, let's set $lastName to an empty string
    $lastName = "Loko na";
}
?>

<?php
include("config.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch programs for the dropdown
$programsResult = $conn->query("SELECT ProgramID, Name FROM programs");

// Fetch criteria for scoring
$criteriaResult = $conn->query("SELECT CriterionID, CriterionName FROM accreditation_criteria");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Form</title>
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
            padding: 20px 100px;
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
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
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
                    <li><a href="dashboardassessor.php">
                            <i class="fa-solid fa-gauge"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                    </li>
                    <li><a href="fdocuments.php">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span class="nav-item">Schedule</span>
                        </a>
                    </li>
                    <li><a href="ftasks.php">
                            <i class="fa-solid fa-file"></i>
                            <span class="nav-item">Tools</span>
                        </a>
                    </li>
                    <li><a href="scommunication.php">
                            <i class="fa-solid fa-bell"></i>
                            <span class="nav-item">Communication</span>
                        </a>
                    </li>
                    <li><a href="fcommunication.php">
                            <i class="fa-solid fa-bell"></i>
                            <span class="nav-item">Documents</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="main">
            <div class="main-flex">
                <div class="main-title">
                    <i class="fa-solid fa-gauge"></i>
                    <h2>Assessor Dashboard</h2>
                </div>
                <div class="main-title">
                    <i class="fa-solid fa-bell"  style="margin-right: 50px; font-size:x-large"></i>
                </div>
            </div>
            <div class="directory">
                <p><a href="dashboardassessor.php">Dashboard</a> > Assessor Dashboard</p>
            </div>
            <div class="main-content">
            <h1>Accreditation Visit Form</h1>
            <form action="process_accreditation.php" method="post" id="accreditationForm">
                <label for="program">Program:</label><br>
                <select name="program" id="program" required>
                    <?php while ($program = $programsResult->fetch_assoc()) : ?>
                        <option value="<?= $program['ProgramID'] ?>"><?= $program['Name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <br><br>

                <label for="visit_date">Visit Date:</label><br>
                <input type="date" id="visit_date" name="visit_date" required>
                <br><br>

                <label for="visit_type">Visit Type:</label><br>
                <select name="visit_type" id="visit_type" required>
                    <option value="Pre-Accreditation Visit">Pre-Accreditation Visit</option>
                    <option value="Initial Accreditation">Initial Accreditation</option>
                    <option value="Reaccreditation">Reaccreditation</option>
                    <option value="Interim Visit">Interim Visit</option>
                    <option value="Special Visit">Special Visit</option>
                </select>
                <br><br>

                <label for="assessment_team">Assessment Team:</label><br>
                <input type="text" id="assessment_team" name="assessment_team" required>
                <br><br>

                <label for="visit_outcome">Visit Outcome:</label><br>
                <input type="text" id="visit_outcome" name="visit_outcome" required>
                <br><br>

                <h2>Scores and Comments</h2>
                <?php while ($criterion = $criteriaResult->fetch_assoc()) : ?>
                    <label for="criterion_<?= $criterion['CriterionID'] ?>"><?= $criterion['CriterionName'] ?>:</label><br>
                    <input type="number" id="criterion_<?= $criterion['CriterionID'] ?>" name="scores[<?= $criterion['CriterionID'] ?>]" min="1" max="5" required onchange="updateComment(<?= $criterion['CriterionID'] ?>)">
                    <input type="text" id="comment_<?= $criterion['CriterionID'] ?>" name="comments[<?= $criterion['CriterionID'] ?>]" readonly> <!-- Hidden input for comments -->
                    <br><br>
                <?php endwhile; ?>

                <label for="overall_comments">Recommendations:</label><br>
                <textarea id="overall_comments" name="overall_comments" rows="4" cols="50"></textarea>
                <br><br>

                <input type="submit" value="Submit">
            </form>

            <script>
                function updateComment(criterionID) {
                    var scoreInput = document.getElementById("criterion_" + criterionID);
                    var commentInput = document.getElementById("comment_" + criterionID);
                    var score = parseInt(scoreInput.value);
                    var comment = '';
                    switch (score) {
                        case 1:
                            comment = 'Poor';
                            break;
                        case 2:
                            comment = 'Below Average';
                            break;
                        case 3:
                            comment = 'Average';
                            break;
                        case 4:
                            comment = 'Good';
                            break;
                        case 5:
                            comment = 'Excellent';
                            break;
                        default:
                            comment = 'Invalid Score';
                    }
                    commentInput.value = comment;
                }
            </script>
            </div>
        </div>
    
</html>
<script src="script.js"></script>

<?php
$conn->close();
?>
