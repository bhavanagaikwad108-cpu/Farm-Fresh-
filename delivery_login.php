<?php
session_start();
include 'db.php';

$message = "";

if(isset($_POST['login']))
{
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM delivery_boy WHERE email='$email'");

    if(mysqli_num_rows($query) > 0)
    {
        $row = mysqli_fetch_assoc($query);

        if($password == $row['password'])
        {
            $_SESSION['delivery_id'] = $row['delivery_id'];
            $_SESSION['delivery_name'] = $row['full_name'];

            header("Location: delivery_dashboard.php");
            exit();
        }
        else
        {
            $message = "Invalid Password";
        }
    }
    else
    {
        $message = "Delivery Account Not Found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Delivery Boy Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#4c1d95,#6d28d9,#9333ea);
    overflow:hidden;
    position:relative;
}

body::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    border-radius:50%;
    background:rgba(255,255,255,0.08);
    top:-150px;
    left:-150px;
}

body::after{
    content:'';
    position:absolute;
    width:400px;
    height:400px;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    bottom:-120px;
    right:-120px;
}

.login-card{
    width:420px;
    background:rgba(255,255,255,0.97);
    padding:40px;
    border-radius:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.25);
    position:relative;
    z-index:1;
}

.logo{
    text-align:center;
    margin-bottom:25px;
}

.delivery-icon{
    font-size:60px;
}

.logo h2{
    color:#6d28d9;
    margin-top:10px;
    font-size:30px;
    font-weight:700;
}

.logo p{
    color:#666;
    font-size:14px;
}

.input-group{
    margin-top:18px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    color:#333;
    font-weight:500;
}

.input-group input{
    width:100%;
    padding:14px;
    border:2px solid #e5e7eb;
    border-radius:12px;
    font-size:15px;
    transition:0.3s;
}

.input-group input:focus{
    outline:none;
    border-color:#7c3aed;
    box-shadow:0 0 10px rgba(124,58,237,0.15);
}

.login-btn{
    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#8b5cf6,#6d28d9);
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(109,40,217,0.35);
}

.message{
    margin-top:18px;
    text-align:center;
    color:red;
    font-weight:600;
}

.footer{
    text-align:center;
    margin-top:20px;
    color:#777;
    font-size:13px;
}

@media(max-width:500px)
{
    .login-card{
        width:92%;
        padding:30px;
    }
}

</style>
</head>

<body>

<div class="login-card">

    <div class="logo">
        <div class="delivery-icon">🚚</div>
        <h2>Fast Delivery</h2>
        <p>Delivery Partner Login Portal</p>
    </div>

    <form method="POST">

        <div class="input-group">
            <label>Email Address</label>
            <input type="email"
                   name="email"
                   placeholder="Enter your email"
                   required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password"
                   name="password"
                   placeholder="Enter your password"
                   required>
        </div>

        <button type="submit" name="login" class="login-btn">
            Login
        </button>

    </form>

    <?php
    if($message != "")
    {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <div class="footer">
        © 2026 Fast Delivery System
    </div>

</div>

</body>
</html>