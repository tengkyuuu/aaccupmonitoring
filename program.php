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
                        <i class="fa-solid fa-user"></i>
                        <span class="nav-item">Users</span>
                    </a>
                    </li>
                    <li><a href="campus.php">
                        <i class="fa-solid fa-school"></i>
                        <span class="nav-item">Campuses</span>
                    </a>
                    </li>
                    <li><a href="departments.php">
                        <i class="fa-solid fa-building-user"></i>
                        <span class="nav-item">Departments  </span>
                    </a>
                    </li>
                    <li><a href="program.php">
                        <i class="fa-solid fa-gear"></i>
                        <span class="nav-item">Programs</span>
                    </a>
                    </li>
                    <li><a href="visits.php">
                        <i class="fa-solid fa-location-dot"></i>
                        <span class="nav-item">Visits</span>
                    </a>
                    </li>
                    <li><a href="documents.php">
                        <i class="fa-solid fa-file"></i>
                        <span class="nav-item">Documents</span>
                    </a>
                    </li>
                    <li><a href="tasks.php">
                        <i class="fa-solid fa-list-check"></i>
                        <span class="nav-item">Tasks</span>
                    </a>
                    </li>
                    <li><a href="acommunication.php">
                        <i class="fa-solid fa-comments"></i>
                        <span class="nav-item">Communication</span>
                    </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main">
            <!-- Add Program Modal -->
            <div id="add-program-modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddProgramModal()">×</span>
                    <h2>Add Program</h2>
                    <form id="add-program-form" action="add_program.php" method="post">
                        <label for="add-program-code">Code:</label>
                        <input type="text" id="add-program-code" name="code" required>
                        <label for="add-program-name">Program Name:</label>
                        <input type="text" id="add-program-name" name="program_name" required>
                        <label for="add-department">Department:</label>
                        <select id="add-department" name="department" required>
                            <?php
                            // Include config.php to establish database connection
                            include("config.php");

                            // SQL query to fetch departments from the database
                            $sql = "SELECT * FROM departments";
                            $result = $conn->query($sql);

                            // Check if departments are found
                            if ($result->num_rows > 0) {
                                // Output data of each row as dropdown options
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row["DepartmentID"] . "'>" . $row["Name"] . "</option>";
                                }
                            } else {
                                echo "<option value=''>No departments found</option>";
                            }
                            ?>
                        </select>
                        <!-- Removed the input for the level -->
                        <input type="hidden" id="add-level" name="level" value="N/A">
                        <button type="submit">Add Program</button>
                    </form>
                </div>
            </div>
            <!-- Edit Program Modal -->
            <div id="edit-program-modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditProgramModal()">×</span>
                    <h2>Edit Program</h2>
                    <form id="edit-program-form" action="edit_program.php" method="post">
                        <!-- Editable fields -->
                        <input type="hidden" id="edit-program-id" name="program_id">
                        <label for="edit-program-code">Code:</label>
                        <input type="text" id="edit-program-code" name="code" required>
                        <label for="edit-program-name">Program Name:</label>
                        <input type="text" id="edit-program-name" name="program_name" required>
                        <label for="edit-department">Department:</label>
                        <select id="edit-department" name="department" required>
                            <!-- Department options will be populated dynamically -->
                        </select>
                        <!-- Hidden input for CSRF protection -->
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="main">
                <div class="main-title">
                    <i class="fa-solid fa-gauge"></i>
                    <h2>Programs</h2>
                </div>
                <div class="directory">
                    <p><a href="dashboardadmin.php">Dashboard</a> > Programs</p>
                </div>
                <div class="main-content">
                    <div class="table-container">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <button onclick="openAddProgramModal()">Add Program</button>
                        <table id="dataTable">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Department</th>
                                <th>Level</th>
                                <th>Accreditation Status</th> <!-- Add the Actions table header -->
                                <th>Accreditation Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php
                                // Include the config.php file
                                include('config.php');

                                // SQL query to retrieve program data with department names
                                $sql = "SELECT programs.ProgramID, programs.Name AS ProgramName, departments.Name AS DepartmentName, programs.Level, programs.AccreditationStatus, programs.AccreditationLevel
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
                                        echo "<td>" . $row["Level"] . "</td>"; // Display level
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
        function openAddProgramModal() {
            var modal = document.getElementById("add-program-modal");
            modal.style.display = "block";
        }

        // Close Add Program Modal
        function closeAddProgramModal() {
            var modal = document.getElementById("add-program-modal");
            modal.style.display = "none";
        }

        // Open Edit Program Modal
        function openEditProgramModal(programId, programCode, programName, departmentId) {
            var modal = document.getElementById("edit-program-modal");
            modal.style.display = "block";
            // Populate form fields with program details
            document.getElementById("edit-program-id").value = programId;
            document.getElementById("edit-program-code").value = programCode;
            document.getElementById("edit-program-name").value = programName;
            document.getElementById("edit-department").value = departmentId;
        }

        // Close Edit Program Modal
        function closeEditProgramModal() {
            var modal = document.getElementById("edit-program-modal");
            modal.style.display = "none";
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