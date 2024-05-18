<?php
session_start(); // Start session

include("config.php");

// Check if the user is logged in before accessing session variables
if (isset($_SESSION['UserID'])) {
    // Fetch user's information from the database
    $userId = $_SESSION['UserID'];
    $query = "SELECT firstName, lastName, img FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $profileImage);
    $stmt->fetch();
    $stmt->close();
} else {
    // Redirect the user to the login page if not logged in
    header("Location: index.php");
    exit();
}

// Store the referring page in session
if (isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['last_page'] = $_SERVER['HTTP_REFERER'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.css" rel="stylesheet">
</head>

<body>
<h2>Edit Profile</h2>
    <form id="edit-profile-form" action="update_profile.php" method="post" enctype="multipart/form-data">
        <label for="profile_image">Profile Image:</label>
        <input type="file" name="profile_image" id="profile_image"><br><br>
        <div>
            <label for="aspect_ratio">Crop Aspect Ratio:</label>
            <select name="aspect_ratio" id="aspect_ratio">
                <option value="1">Square</option>
                <!-- You can add more aspect ratios here if needed -->
            </select>
        </div><br>
        <img id="preview" src="<?php echo $profileImage; ?>" alt="Profile Image" width="100"><br><br>
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo isset($firstName) ? $firstName : ''; ?>"><br><br>
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo isset($lastName) ? $lastName : ''; ?>"><br><br>
        <button type="submit" name="submit">Save Changes</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/cropperjs"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var profileImage = document.getElementById('profile_image');
            var preview = document.getElementById('preview');
            var cropper;

            profileImage.addEventListener('change', function (event) {
                var files = event.target.files;
                var file = files[0];
                var reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(preview, {
                        aspectRatio: parseInt(document.getElementById('aspect_ratio').value),
                        viewMode: 2,
                        autoCropArea: 1,
                    });
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
</body>

</html>
