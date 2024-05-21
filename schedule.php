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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch visit dates and statuses
$sql = "SELECT VisitDate, Status FROM accreditationvisits";
$result = $conn->query($sql);

$visits = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visits[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessor Schedule</title>
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
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            padding: 50px;
        }
        .day {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .day.scheduled {
            background-color: red;
            color: white;
        }
        .day.in-progress {
            background-color: green;
            color: white;
        }
        .day.completed {
            background-color: blue;
            color: white;
        }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .navigation button {
            padding: 10px;
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
                    <li><a href="dashboardassessor.php">
                            <i class="fa-solid fa-gauge"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                    </li>
                    <li><a href="schedule.php">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span class="nav-item">Schedule</span>
                        </a>
                    </li>
                    <li><a href="accreditation_form.php">
                            <i class="fa-brands fa-wpforms"></i>
                            <span class="nav-item">Form</span>
                        </a>
                    </li>
                    <li><a href="scommunication.php">
                            <i class="fa-solid fa-comments"></i>
                            <span class="nav-item">Communication</span>
                        </a>
                    </li>
                    <li><a href="report.php">
                            <i class="fa-solid fa-flag"></i>
                            <span class="nav-item">Reports</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        
        <div class="main">
            <div class="main-flex">
                <div class="main-title">
                    <i class="fa-solid fa-gauge"></i>
                    <h2>Assessor Schedule</h2>
                </div>
                <div class="main-title">
                    <i id="bell-icon" class="fa-solid fa-bell" style="margin-right: 50px; font-size: x-large"></i>
                </div>
            </div>
            <div id="notification-popup" class="notification-popup">
                <div class="notification-content">
                    <!-- Notifications will be dynamically added here -->
                </div>
            </div>
            <div class="directory">
                <p><a href="dashboarduser.php">Dashboard</a> > Assessor Schedule</p>
            </div>
            
            <div class="main-content">
            
    <div class="navigation">
        <button id="prevMonth">Previous</button>
        <div id="currentMonthYear"></div>
        <button id="nextMonth">Next</button>
    </div>
    <div class="calendar" id="calendar">
        <!-- Calendar will be generated here -->
    </div>
    <script>
        const visits = <?php echo json_encode($visits); ?>;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        function daysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        }

        function generateCalendar(month, year) {
            const calendar = document.getElementById('calendar');
            const currentMonthYear = document.getElementById('currentMonthYear');
            calendar.innerHTML = '';
            currentMonthYear.innerText = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = daysInMonth(month, year);

            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('day');
                calendar.appendChild(emptyDay);
            }

            for (let day = 1; day <= totalDays; day++) {
                const date = new Date(year, month, day);
                const formattedDate = date.toISOString().split('T')[0];
                const visit = visits.find(v => v.VisitDate === formattedDate);

                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.innerText = day;

                if (visit) {
                    if (visit.Status === 'Scheduled') {
                        dayDiv.classList.add('scheduled');
                    } else if (visit.Status === 'In Progress') {
                        dayDiv.classList.add('in-progress');
                    } else if (visit.Status === 'Completed') {
                        dayDiv.classList.add('completed');
                    }
                }

                calendar.appendChild(dayDiv);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            if (currentMonth === 0) {
                currentMonth = 11;
                currentYear--;
            } else {
                currentMonth--;
            }
            generateCalendar(currentMonth, currentYear);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            if (currentMonth === 11) {
                currentMonth = 0;
                currentYear++;
            } else {
                currentMonth++;
            }
            generateCalendar(currentMonth, currentYear);
        });

        generateCalendar(currentMonth, currentYear);
    </script>
            </div>
        </div>
    
</body>
<script>
    // Function to handle bell icon click event
document.getElementById('bell-icon').addEventListener('click', function() {
    // Display the notification popup
    document.getElementById('notification-popup').style.display = 'block';
    
    // AJAX request to fetch and display notifications
    fetchNotifications();
});

// AJAX request to periodically check for new notifications
setInterval(function() {
    checkForNewNotifications();
}, 5000); // Check every 5 seconds

// Function to check for new notifications
function checkForNewNotifications() {
    $.ajax({
        url: 'check_for_new_notifications.php',
        type: 'GET',
        success: function(response) {
            if (response === 'true') {
                // If there are new notifications, change the bell icon color to red
                document.getElementById('bell-icon').style.color = 'red';
            } else {
                // If there are no new notifications, reset the bell icon color
                document.getElementById('bell-icon').style.color = ''; // Reset to default color
            }
        }
    });
}

// Function to fetch and display notifications
function fetchNotifications() {
    $.ajax({
        url: 'fetch_notifications.php',
        type: 'GET',
        success: function(response) {
            // Display notifications in the notification popup
            document.querySelector('.notification-content').innerHTML = response;
        }
    });
}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="script.js"></script>
</html>
