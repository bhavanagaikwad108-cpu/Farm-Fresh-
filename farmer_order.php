<?php
session_start();
include("db.php");

if(isset($_POST['register']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile_no = $_POST['mobile_no'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO farmer (name, email, mobile_no, address, password)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $mobile_no, $address, $hashed_password);

    $result = mysqli_stmt_execute($stmt);

    if($result)
    {
        $_SESSION['farmer_id'] = mysqli_insert_id($conn);
        $_SESSION['farmer_name'] = $name;

        header("Location: farmer_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Farmer Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;

    /* premium farm background */
    background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
                url('https://images.unsplash.com/photo-1500382017468-9049fed747ef');
    background-size: cover;
    background-position: center;
}

/* CENTER */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* CARD */
.register-box {
    width: 420px;
    padding: 35px;
    border-radius: 18px;

    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(15px);

    box-shadow: 0 15px 40px rgba(0,0,0,0.4);

    animation: fadeUp 1s ease;
}

/* smooth entry */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 🌿 FLOATING LOGO */
.logo {
    display: block;
    margin: 0 auto 15px;
    width: 95px;

    animation: floatLogo 4s ease-in-out infinite;
}

@keyframes floatLogo {
    0%   { transform: translateY(0px) rotate(0deg); }
    50%  { transform: translateY(-10px) rotate(8deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

/* TITLE */
h2 {
    text-align: center;
    color: #eaffea;
    font-weight: 600;
}

/* INPUT STYLE */
input, textarea {
    width: 100%;
    padding: 13px;
    margin: 10px 0;

    border: none;
    border-radius: 10px;

    outline: none;
    font-size: 14px;

    transition: 0.3s;

    background: rgba(255,255,255,0.85);
}

/* glow focus */
input:focus, textarea:focus {
    box-shadow: 0 0 12px rgba(0,255,100,0.4);
    transform: scale(1.02);
}

/* 🚀 REGISTER BUTTON */
input[type="submit"] {
    background: linear-gradient(90deg, #2e7d32, #43a047);
    color: white;
    font-weight: bold;
    cursor: pointer;

    transition: all 0.3s ease;
    border: none;
}

/* hover animation */
input[type="submit"]:hover {
    transform: scale(1.07);
    box-shadow: 0 12px 25px rgba(0,255,100,0.3);
}

/* click effect */
input[type="submit"]:active {
    transform: scale(0.97);
}

/* FOOTER */
.footer {
    text-align: center;
    font-size: 12px;
    color: #ddd;
    margin-top: 10px;
}
</style>

</head>

<body>

<div class="container">
    <div class="register-box">

        <!-- LOGO -->
        <img src="https://cdn-icons-png.flaticon.com/512/2909/2909765.png" class="logo">

        <h2>Farmer Registration</h2>

        <!-- SAME FORM (NO LOGIC CHANGE) -->
        <form method="post">

            <input type="text" name="name" placeholder="Full Name" required>

            <input type="email" name="email" placeholder="Email Address" required>

            <input type="text" name="mobile_no" placeholder="Mobile Number" required>

            <textarea name="address" placeholder="Address" required></textarea>

            <input type="password" name="password" placeholder="Password" required>

            <input type="submit" name="register" value="Register">
           

        </form>

        <div class="footer">
            © 2026 Online Farming System
        </div>

    </div>
</div>

</body>
</html>