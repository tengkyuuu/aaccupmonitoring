<?php
session_start(); // Start session

include("config.php");

// Check if the user is logged in before accessing session variables
if (isset($_SESSION['UserID'])) {
    // Fetch user's information from the database
    $userId = $_SESSION['UserID'];

    // Handle form submission
    if (isset($_POST['submit'])) {
        // Check if a file is uploaded
        if (isset($_FILES['profile_image'])) {
            $targetDirectory = "profile_images/"; // Directory where profile images will be uploaded
            $targetFile = $targetDirectory . basename($_FILES['profile_image']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if file is an image
            $check = getimagesize($_FILES['profile_image']['tmp_name']);
            if ($check !== false) {
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Check if uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                } else {
                    // If everything is ok, try to upload file
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                        // Update user's profile image in the database
                        $query = "UPDATE users SET img = ? WHERE UserID = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("si", $targetFile, $userId);
                        if ($stmt->execute()) {
                            echo "Profile image updated successfully.";
                        } else {
                            echo "Error updating profile image: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Update first name and last name
        if (isset($_POST['first_name']) && isset($_POST['last_name'])) {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];

            // Update user's first name and last name in the database
            $query = "UPDATE users SET firstName = ?, lastName = ? WHERE UserID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $firstName, $lastName, $userId);
            if ($stmt->execute()) {
                echo "Profile information updated successfully.";
            } else {
                echo "Error updating profile information: " . $stmt->error;
            }
            $stmt->close();
        }
        
        // Redirect to the last page
        if (isset($_SESSION['last_page'])) {
            header('Location: ' . $_SESSION['last_page']);
            exit();
        } else {
            // Redirect to a default page if no last page is found
            header('Location: profile.php');
            exit();
        }
    } else {
        echo "Form submission failed.";
    }
} else {
    // Redirect the user to the login page if not logged in
    header("Location: index.php");
    exit();
}
?>
