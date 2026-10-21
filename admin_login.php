<?php
session_start();
include("db.php");

if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username == "admin" && $password == "admin123")
    {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    }
    else
    {
        echo "<script>alert('Invalid Admin Username or Password');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Online Farming System</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            width: 380px;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 15px;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: green;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            background: #2e7d32;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background: #1b5e20;
        }

        .footer-text {
            margin-top: 15px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-box">

        <!-- Logo Image -->
        <img src="https://cdn-icons-png.flaticon.com/512/2909/2909768.png" class="logo" alt="Farm Logo">

        <h2>Admin Login</h2>

        <form method="post">
            <input type="text" name="username" placeholder="Enter Admin Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="submit" name="login" value="Login">
        </form>

        <div class="footer-text">
            © 2026 Online Farming System
        </div>

    </div>
</div>

</body>
</html>