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
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $campus = $_POST['campus'];
    $programAffiliated = $_POST['program'];

    // Generate password as the last name in all caps
    $password = strtoupper($lastName);

    // SQL query to insert new user
    $sql = "INSERT INTO users (firstName, lastName, email, Role, Campus, programAffiliated, Password) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssis", $firstName, $lastName, $email, $role, $campus, $programAffiliated, $password);

        // Execute statement
        if ($stmt->execute()) {
            // Redirect to users page
            header("Location: user.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close connection
    $conn->close();
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
            background: linear-gradient(700deg, rgb(48, 48, 48), orangered);
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
            <div id="add-user-form" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeAddForm()">×</span>
                    <h2>Add New User</h2>
                    <form action="user.php" method="post">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="first_name" required>
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="last_name" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <?php
                        // Fetch roles from the database and populate the dropdown
                        $rolesResult = $conn->query("SHOW COLUMNS FROM users WHERE Field = 'Role'");
                        $row = $rolesResult->fetch_assoc();
                        $enumList = explode(",", str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type'])-6))));
                        foreach ($enumList as $value) {
                            echo "<option value='" . $value . "'>" . $value . "</option>";
                        }
                        ?>
                    </select>
                    <label for="campus">Campus:</label>
                    <select id="campus" name="campus" required>
                        <?php
                        // Fetch campuses from the database and populate the dropdown
                        $campusesResult = $conn->query("SELECT UniversityID, Name FROM universities");
                        while ($row = $campusesResult->fetch_assoc()) {
                            echo "<option value='" . $row['UniversityID'] . "'>" . $row['Name'] . "</option>";
                        }
                        ?>
                    </select>
                    <label for="program">Program Affiliation:</label>
                    <select id="program" name="program" required>
                        <?php
                        // Fetch programs from the database and populate the dropdown
                        $programsResult = $conn->query("SELECT ProgramID, Name FROM programs");
                        while ($row = $programsResult->fetch_assoc()) {
                            echo "<option value='" . $row['ProgramID'] . "'>" . $row['Name'] . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Add User</button>
                    </form>
                </div>
            </div>
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
                    <button onclick="toggleAddForm()">Add User</button>
                        <table id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Campus</th>
                                <th>Actions</th> <!-- Add the Actions table header -->
                            </tr>
                        </thead>
                            <tbody>
                            <?php
                                // Include the config.php file
                                include('config.php');

                                // SQL query to retrieve user data with university name instead of campus ID
                                $sql = "SELECT u.UserID, u.uniqueID, CONCAT(u.firstName, ' ', u.lastName) AS fullName, u.email, u.Role, univ.Name AS UniversityName 
                                        FROM users u 
                                        LEFT JOIN universities univ ON u.Campus = univ.UniversityID";
                                $result = $conn->query($sql);

                                // Display user data in the table
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["uniqueID"] . "</td>"; // Display uniqueID in the first column
                                        echo "<td>" . $row["fullName"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>" . $row["Role"] . "</td>";
                                        echo "<td>" . $row["UniversityName"] . "</td>"; // Display university name instead of campus ID
                                        echo "<td><button onclick=\"toggleEditForm('" . $row["uniqueID"] . "', '" . $row["fullName"] . "', '" . $row["email"] . "', '" . $row["Role"] . "', '" . $row["UniversityName"] . "')\">Edit</button> <button onclick=\"confirmDelete('" . $row["UserID"] . "')\"><i class='fa-solid fa-trash'></i></button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No users found</td></tr>";
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
        function confirmDelete(userID) {
            var confirmDelete = confirm("Are you sure you want to delete this user?");
            if (confirmDelete) {
                // If user confirms, redirect to delete_user.php with user ID
                window.location.href = "delete_user.php?userID=" + userID;
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

        function toggleAddForm() {
            var addUserForm = document.getElementById("add-user-form");
            addUserForm.style.display = "block";
        }

        function closeAddForm() {
            var addUserForm = document.getElementById("add-user-form");
            addUserForm.style.display = "none";
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