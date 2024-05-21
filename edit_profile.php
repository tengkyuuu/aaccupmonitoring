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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs/dist/cropper.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        html, body {
            height: 100%;
            background: #18191A;
            color: #333;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .form-popup {
            max-width: 600px;
            width: 100%;
            background: #fff;
            border: 2px solid #fff;
            border-radius: 20px;
            box-shadow: 0 20px 35px rgba(0, 0.1, 0.9);
            padding: 20px;
            text-align: center;
        }

        .form-popup h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-popup img {
            width: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .form-popup .input-field {
            margin-bottom: 20px;
            position: relative;
        }

        .form-popup .input-field input,
        .form-popup .input-field select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .form-popup .input-field label {
            position: absolute;
            top: 50%;
            left: 15px;
            color: #666;
            pointer-events: none;
            transform: translateY(-50%);
            transition: 0.2s ease;
        }

        .form-popup .input-field input:focus {
            border-color: #0080ff;
        }

        .form-popup .input-field input:is(:focus, :valid) {
            padding: 16px 15px 0;
        }

        .form-popup .input-field input:is(:focus, :valid) ~ label {
            color: #0080ff;
            font-size: 0.75rem;
            transform: translateY(-120%);
        }

        .form-popup button {
            width: 100%;
            padding: 14px 0;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            background: black;
            color: #fff;
            transition: background 0.2s ease;
        }

        .form-popup button:hover {
            background: #0080ff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-popup">
            <h2>Edit Profile</h2>
            <form id="edit-profile-form" action="update_profile.php" method="post" enctype="multipart/form-data">
                <img id="preview" src="<?php echo $profileImage; ?>" alt="Profile Image"><br><br>
                <div class="input-field">
                    <input type="file" name="profile_image" id="profile_image">
                    <label for="profile_image">Profile Image</label>
                </div>
                <div class="input-field">
                    <input type="text" name="first_name" id="first_name" value="<?php echo isset($firstName) ? $firstName : ''; ?>" required>
                    <label for="first_name">First Name</label>
                </div>
                <div class="input-field">
                    <input type="text" name="last_name" id="last_name" value="<?php echo isset($lastName) ? $lastName : ''; ?>" required>
                    <label for="last_name">Last Name</label>
                </div>
                <button type="submit" name="submit">Save Changes</button>
            </form>
        </div>
    </div>

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
                        aspectRatio: 1,
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
