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
                    <li><a href="fcommunication.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Communication</span>
                    </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main">
            <div class="main">
                <div class="main-title">
                    <i class="fa-solid fa-gauge"></i>
                    <h2>Staff Users</h2>
                </div>
                <div class="directory">
                    <p><a href="dashboardadmin.php">Dashboard</a> > Staff Users</p>
                </div>
                <div class="main-content">
                    <div class="table-container">
                    <input type="text" id="searchInput" placeholder="Search...">
                        <table id="dataTable">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Department</th>
                                <th>Accreditation Status</th> <!-- Add the Actions table header -->
                                <th>Accreditation Level</th>
                                <th>Results</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php
                                // Include the config.php file
                                include('config.php');

                                // SQL query to retrieve program data with department names
                                $sql = "SELECT programs.ProgramID, programs.code AS ProgramName, departments.Name AS DepartmentName, programs.Level, programs.AccreditationStatus, programs.AccreditationLevel
                                        FROM programs
                                        INNER JOIN departments ON programs.DepartmentID = departments.DepartmentID";
                                // Execute the query
                                $result = $conn->query($sql);

                                // Display program data in the table
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["ProgramName"] . "</td>"; // Display program name
                                        echo "<td>" . $row["DepartmentName"] . "</td>"; // Display department name
                                        echo "<td>" . $row["AccreditationStatus"] . "</td>"; // Display accreditation status
                                        echo "<td>" . $row["AccreditationLevel"] . "</td>"; // Display accreditation level
                                        // Add Edit and Delete buttons
                                        echo "<td><button onclick=\"toggleEditForm('" . $row["ProgramID"] . "', '" . $row["ProgramName"] . "', '" . $row["DepartmentName"] . "', '" . $row["Level"] . "', '" . $row["AccreditationStatus"] . "', '" . $row["AccreditationLevel"] . "')\">Edit</button><button onclick=\"confirmDelete('" . $row["ProgramID"] . "')\">Delete</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No programs found</td></tr>";
                                }

                                // Close database connection
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            
        </div>
    </section>
    <script>
        // Function to prompt user for confirmation before deletion
        function confirmDelete(code) {
            var confirmDelete = confirm("Are you sure you want to delete this program?");
            if (confirmDelete) {
                // If user confirms, redirect to delete_program.php with program code
                window.location.href = "delete_program.php?code=" + code;
            }
        }

        // Function to toggle visibility of the edit program form
        function toggleEditForm(studentID, fullName, email, program, currentYear) {
            var editProgramForm = document.getElementById("edit-program-form");
            editProgramForm.style.display = "block";
            
            // Populate hidden field with studentID
            var editIDField = document.getElementById("edit-student-id");
            editIDField.value = studentID;
            
            // Populate input fields with current values
            var editFullNameField = document.getElementById("edit-full-name");
            editFullNameField.value = fullName;
            
            var editEmailField = document.getElementById("edit-email");
            editEmailField.value = email;
            
            var editProgramField = document.getElementById("edit-program");
            editProgramField.value = program;
            
            var editYearField = document.getElementById("edit-year");
            editYearField.value = currentYear;
        }

        function closeEditForm() {
            var editProgramForm = document.getElementById("edit-program-form");
            editProgramForm.style.display = "none";
        }
    </script>
    <script>
        function searchTable() {
            // Declare variables
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("dataTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those that don't match the search query
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break; // Show the row if any cell matches the search query
                        } else {
                            tr[i].style.display = "none"; // Hide the row if no cell matches the search query
                        }
                    }
                }
            }
        }

        // Attach an event listener to the search input field
        document.getElementById("searchInput").addEventListener("keyup", searchTable);
    </script>
</body>
<script src="script.js"></script>
</html>