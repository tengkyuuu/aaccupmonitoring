<?php
include("config.php");

session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to check user credentials and retrieve role
    $sql = "SELECT UserID, Role FROM Users WHERE email = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
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
        echo "Invalid username or password.";
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
        
        html {
            height: 100%;
            margin: 0;
            
            background: #18191A;
        }
        .form-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            max-width: 50%;
            width: 100%;
            background: whitesmoke;
            border: 2px solid #fff;
            border-radius: 20px;
            transform: translate(-50%, -50%);
            justify-content: center;
            align-self: center;
            box-shadow: 0 20px 35px rgba(0, 0.1, 0.9);
        }
        .form-popup .form-box {
            display: flex;
        }
        .form-box .form-details {
            max-width: 330px;
        }
        .form-box h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-box .form-content {
            width: 100%;
            padding: 35px;
        }
        .form-details img {
            width: 150px;
            margin-top: 50%;
            margin-bottom: 50%;
            margin-right: 20px;
            margin-left: 20px;
        }
        form .input-field {
            height: 50px;
            width: 100%;
            margin-top: 20px;
            position: relative;
        }
        form .input-field input {
            width: 100%;
            height: 100%;
            outline: none;
            padding-left: 0 15px;
            font-size: 0.95rem;
            border-radius: 3px;
            border: 1px solid #18191A;
        }
        form .input-field label {
            position: absolute;
            top: 50%;
            left: 15px;
            color: #18191A;
            pointer-events: none;
            transform: translateY(-50%);
            transition: 0.2s ease;
        }
        .input-field input:focus {
            border-color: 1px solid orange;
        }
        .input-field input:is(:focus, :valid) {
            padding: 16px 15px 0;
        }
        .input-field input:is(:focus, :valid) ~label {
            color: orangered;
            font-size: 0.75rem;
            transform: translateY(-120%);
        }
        .form-box a {
            color: orangered;
            text-decoration: none;
        }
        .form-box a:hover {
            text-decoration: underline;
        }
        form :where(.forgot-pass-link, .policy-text) {
            display: inline-flex;
            margin-top: 13px;
            font-size: 0.95rem;
        }
        form button {
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
        form button:hover {
            background: orangered;
        }
        .form-content .bottom-link {
            text-align: center;
        }
        .form-popup .signup,
        .form-popup.show-signup .login {
            display: none;
        }
        .form-popup.show-signup .signup {
            display: flex;
        }
        .other-options {
           text-align: center;
        }
        
                
    </style>
</head>
<body>
    <div class="form-popup">
        <div class="form-box">
            <div class="form-details">
                <img src="images/loginimg.png">
            </div>
            <div class="form-content">
                <h2>LOGIN</h2>
                <form id="login-form" method="post">
                    <div class="input-field">
                        <input type="text" name="email" required>
                        <label>Email</label>
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
