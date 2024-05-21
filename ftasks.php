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
    // For now, let's set $lastName to an empty string
    $lastName = "Loko na";
}

// Function to shorten task description
function shortenDescription($description, $wordLimit) {
    $words = explode(" ", $description);
    if (count($words) > $wordLimit) {
        return implode(" ", array_slice($words, 0, $wordLimit)) . "...";
    } else {
        return $description;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Tasks</title>
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
        #one {
            background-color: #00c0ef;
        }
        #two {
            background-color: #00a65a;
        }
        #three {
            background-color: #f39c12;
        }
        #one i {
            color: #0083a3;
        }
        #two i {
            color: #00733e;
        }
        #three i {
            color: #b06f09;
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
                    <li><a href="dashboardfaculty.php">
                            <i class="fa-solid fa-gauge"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                    </li>
                    <li><a href="fdocuments.php">
                            <i class="fa-solid fa-file"></i>
                            <span class="nav-item">Documents</span>
                        </a>
                    </li>
                    <li><a href="ftasks.php">
                            <i class="fa-solid fa-list-check"></i>
                            <span class="nav-item">Tasks</span>
                        </a>
                    </li>
                    <li><a href="fcommunication.php">
                            <i class="fa-solid fa-comments"></i>
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
                    <h2>Faculty Tasks</h2>
                </div>
                <div class="main-title">
                    <i class="fa-solid fa-bell"  style="margin-right: 50px; font-size:x-large"></i>
                </div>
            </div>
            <div class="directory">
                <p><a href="dashboarduser.php">Dashboard</a> > Faculty Tasks</p>
            </div>

            <div id="viewMorePopup" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeViewMorePopup()">&times;</span>
                    <h2>Task Details</h2>
                    <p id="taskDescription">
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
                    </p>
                    <!-- Add other task details here -->
                </div>
            </div>

            <div id="editPopup" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditPopup()">&times;</span>
                    <h2>Edit Task Status</h2>
                    <form action="update_task_status.php" method="post">
                        <input type="hidden" name="taskID" id="editTaskID">
                        <label for="status">Status:</label>
                        <select id="status" name="status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <input type="submit" value="Update Status">
                    </form>
                </div>
            </div>
            
            <div class="main-content">
                <h2>Assigned Tasks</h2>
                <table>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Description</th>
                        <th>Assignee</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th> <!-- New header for actions -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Fetch tasks from the database
                        $sql = "SELECT tasks.TaskID, programs.code AS ProgramCode, tasks.Description, tasks.Assignee, tasks.DueDate, tasks.Status
                                FROM tasks
                                LEFT JOIN programs ON tasks.ProgramID = programs.ProgramID";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["TaskID"] . "</td>";
                                echo "<td>" . shortenDescription($row["Description"], 7) . "</td>";
                                echo "<td>" . $row["Assignee"] . "</td>";
                                echo "<td>" . $row["DueDate"] . "</td>";
                                echo "<td>" . $row["Status"] . "</td>";
                                // Action column with icons to open pop-up forms
                                echo "<td>";
                                echo "<button onclick=\"openViewMorePopup('" . $row["TaskID"] . "')\"><i class='far fa-eye'></i></button>"; // Eye icon for View More
                                echo "<button onclick=\"openEditPopup('" . $row["TaskID"] . "')\"><i class='far fa-edit'></i></button>"; // Edit icon for Edit
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No tasks found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            // Function to open View More Popup
            function openViewMorePopup(taskID) {
                // Display the pop-up form
                document.getElementById('viewMorePopup').style.display = 'block';
                // Fetch and display task details using AJAX if needed
                // For now, let's assume the task description is already loaded in the table
                // You can fetch and populate other details as required
                document.getElementById('taskDescription').innerHTML = "Task Description for Task ID: " + taskID;
            }

            // Function to close View More Popup
            function closeViewMorePopup() {
                // Hide the pop-up form
                document.getElementById('viewMorePopup').style.display = 'none';
            }

            // Function to open Edit Popup
            function openEditPopup(taskID) {
                // Display the pop-up form
                document.getElementById('editPopup').style.display = 'block';
                // Set the task ID in a hidden field for form submission
                document.getElementById('editTaskID').value = taskID;
            }

            // Function to close Edit Popup
            function closeEditPopup() {
                // Hide the pop-up form
                document.getElementById('editPopup').style.display = 'none';
            }
        </script>
        <script src="script.js"></script>
</body>


</html>
