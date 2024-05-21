<?php
include("config.php");

session_start();

if (isset($_POST['submit'])) {
    $uniqueID = $_POST['unique_id'];
    $password = $_POST['password'];

    // SQL query to check user credentials and retrieve role
    $sql = "SELECT UserID, Role FROM Users WHERE UniqueID = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $uniqueID, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Set UserID and Role in session
        $_SESSION['UserID'] = $row['UserID'];
        $_SESSION['Role'] = $row['Role'];

        // Redirect user based on role
        switch ($_SESSION['Role']) {
            case 'Administrator':
                header("Location: dashboardadmin.php");
                exit();
            case 'Faculty':
                header("Location: dashboardfaculty.php");
                exit();
            case 'Assessor':
                header("Location: dashboardassessor.php");
                exit();
            default:
                echo "Invalid role.";
                exit();
        }
    } else {
        echo "Invalid unique ID or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #18191A;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url(images/background.png) no-repeat center center/cover;
            filter: blur(10px);
            z-index: -1;
        }

        .form-popup {
            position: relative;
            width: 100%;
            max-width: 600px;
            background: whitesmoke;
            border: 2px solid #fff;
            border-radius: 20px;
            box-shadow: 0 20px 35px rgba(0, 0.1, 0.9);
            z-index: 1;
        }

        .form-box {
            display: flex;
        }

        .form-details {
            max-width: 330px;
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-content {
            width: 100%;
            padding: 35px;
        }

        .form-details img {
            width: 150px;
            margin: 50% 20px;
        }

        .input-field {
            height: 50px;
            width: 100%;
            margin-top: 20px;
            position: relative;
        }

        .input-field input {
            width: 100%;
            height: 100%;
            outline: none;
            padding: 0 15px;
            font-size: 0.95rem;
            border-radius: 3px;
            border: 1px solid #18191A;
        }

        .input-field label {
            position: absolute;
            top: 50%;
            left: 15px;
            color: #18191A;
            pointer-events: none;
            transform: translateY(-50%);
            transition: 0.2s ease;
        }

        .input-field input:focus {
            border-color: lightblue;
        }

        .input-field input:is(:focus, :valid) {
            padding: 16px 15px 0;
        }

        .input-field input:is(:focus, :valid) ~ label {
            color: blue;
            font-size: 0.75rem;
            transform: translateY(-120%);
        }

        .form-box a {
            color: blue;
            text-decoration: none;
        }

        .form-box a:hover {
            text-decoration: underline;
        }

        .forgot-pass-link, .policy-text {
            display: inline-flex;
            margin-top: 13px;
            font-size: 0.95rem;
        }

        button {
            width: 100%;
            color: #fff;
            border: none;
            outline: none;
            padding: 14px 0;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 3px;
            cursor: pointer;
            margin: 25px 0;
            background: black;
            transition: 0.2s ease;
        }

        button:hover {
            background: blue;
        }

        .bottom-link {
            text-align: center;
        }

        .signup, .show-signup .login {
            display: none;
        }

        .show-signup .signup {
            display: flex;
        }

        .other-options {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="form-popup">
        <div class="form-box">
            <div class="form-details">
                <img src="images/loginimg.png">
            </div>
            <div class="form-content">
                <h2>LOGIN</h2>
                <form id="login-form" method="post">
                    <div class="input-field">
                        <input type="text" name="unique_id" required>
                        <label>ID</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <button type="submit" name="submit">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
