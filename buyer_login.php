<?php
session_start();
include("db.php");

$error = "";

if(isset($_POST['login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* CHECK EMAIL */

    $query = mysqli_query($conn,"
    SELECT *
    FROM buyer
    WHERE email='$email'
    ");

    if(mysqli_num_rows($query) > 0)
    {
        $buyer = mysqli_fetch_assoc($query);

        /* VERIFY HASH PASSWORD */

        if(password_verify($password, $buyer['password']))
        {
            $_SESSION['buyer_id'] = $buyer['buyer_id'];
            $_SESSION['buyer_name'] = $buyer['name'];

            header("Location: buyer_dashboard.php");
            exit();
        }
        else
        {
            $error = "Incorrect Password";
        }
    }
    else
    {
        $error = "Email Not Found";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Buyer Login</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#581c87,#7e22ce,#9333ea);
}

/* LOGIN BOX */

.login-box{
    width:400px;
    background:white;
    padding:35px;
    border-radius:25px;
    box-shadow:0 10px 35px rgba(0,0,0,0.2);
    text-align:center;
}

/* ICON */

.logo{
    width:90px;
    height:90px;
    margin:auto;
    margin-bottom:15px;
    background:#f3e8ff;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
}

.logo i{
    font-size:42px;
    color:#7e22ce;
}

/* TITLE */

h2{
    margin-bottom:8px;
    color:#581c87;
}

.subtitle{
    color:#64748b;
    margin-bottom:25px;
    font-size:14px;
}

/* INPUT */

.input-box{
    margin-bottom:18px;
    position:relative;
}

.input-box input{
    width:100%;
    padding:14px 14px 14px 45px;
    border:1px solid #d8b4fe;
    border-radius:12px;
    font-size:15px;
}

.input-box i{
    position:absolute;
    left:15px;
    top:15px;
    color:#7e22ce;
}

/* BUTTON */

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#7e22ce,#9333ea);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.02);
}

/* ERROR */

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:12px;
    border-radius:10px;
    margin-bottom:18px;
    font-size:14px;
}

/* REGISTER */

.register-link{
    margin-top:20px;
    font-size:14px;
}

.register-link a{
    color:#7e22ce;
    text-decoration:none;
    font-weight:600;
}

</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        <i class="fa fa-user"></i>
    </div>

    <h2>Buyer Login</h2>

    <p class="subtitle">
        Access your agriculture marketplace account
    </p>

    <?php if(!empty($error)) { ?>

        <div class="error">
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="input-box">

            <i class="fa fa-envelope"></i>

            <input
            type="email"
            name="email"
            placeholder="Enter Email"
            required
            >

        </div>

        <div class="input-box">

            <i class="fa fa-lock"></i>

            <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
            >

        </div>

        <button type="submit" name="login">
            Login
        </button>

    </form>

    <div class="register-link">

        Don't have an account?

        <a href="buyer_register.php">
            Register
        </a>

    </div>

</div>

</body>
</html>