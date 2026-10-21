<?php
session_start();
include("db.php");

$message = "";

if(isset($_POST['register']))
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile_no = trim($_POST['mobile_no']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    /* VALIDATION */

    if(
        empty($name) ||
        empty($email) ||
        empty($mobile_no) ||
        empty($address) ||
        empty($password) ||
        empty($confirm_password)
    ){
        $message = "All fields are required.";
    }

    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Invalid email format.";
    }

    elseif(!preg_match('/^[0-9]{10}$/', $mobile_no)){
        $message = "Mobile number must be 10 digits.";
    }

    elseif($password != $confirm_password){
        $message = "Passwords do not match.";
    }

    else{

        /* CHECK EMAIL */

        $check_email = mysqli_query($conn,"
        SELECT * FROM buyer
        WHERE email='$email'
        ");

        if(mysqli_num_rows($check_email) > 0){

            $message = "Email already registered.";

        } else {

            /* HASH PASSWORD */

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            /* INSERT DATA */

            $sql = "
            INSERT INTO buyer
            (
                name,
                mobile_no,
                address,
                email,
                password
            )
            VALUES
            (
                '$name',
                '$mobile_no',
                '$address',
                '$email',
                '$hashed_password'
            )
            ";

            $result = mysqli_query($conn, $sql);

            if($result)
            {
                $buyer_id = mysqli_insert_id($conn);

                $_SESSION['buyer_id'] = $buyer_id;
                $_SESSION['buyer_name'] = $name;

                header("Location: buyer_dashboard.php");
                exit();
            }
            else
            {
                $message = "Registration Failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Buyer Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;

    background:
    linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
    url('https://images.unsplash.com/photo-1500382017468-9049fed747ef');

    background-size:cover;
    background-position:center;

    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* MAIN BOX */

.register-container{
    width:100%;
    max-width:540px;
}

.register-box{
    background:rgba(255,255,255,0.97);
    border-radius:28px;
    padding:35px;
    box-shadow:0 12px 35px rgba(0,0,0,0.35);
}

/* LOGO */

.logo-section{
    text-align:center;
    margin-bottom:28px;
}

.logo{
    width:95px;
    height:95px;
    object-fit:contain;
    background:#f3e8ff;
    padding:14px;
    border-radius:50%;
    margin-bottom:15px;
}

.logo-section h2{
    color:#6d28d9;
    font-size:32px;
    font-weight:700;
}

.logo-section p{
    color:#64748b;
    font-size:14px;
    margin-top:6px;
}

/* ALERT */

.alert{
    background:#fee2e2;
    color:#991b1b;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-size:14px;
    text-align:center;
}

/* INPUT */

.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    margin-bottom:7px;
    font-size:14px;
    font-weight:600;
    color:#334155;
}

.input-box{
    position:relative;
}

.input-box i{
    position:absolute;
    left:15px;
    top:16px;
    color:#7c3aed;
    font-size:15px;
}

.input-box input,
.input-box textarea{
    width:100%;
    padding:14px 14px 14px 45px;
    border-radius:14px;
    border:1px solid #cbd5e1;
    font-size:14px;
    transition:0.3s;
    background:#fff;
}

.input-box textarea{
    resize:none;
    height:90px;
    padding-top:14px;
}

.input-box input:focus,
.input-box textarea:focus{
    border-color:#7c3aed;
    outline:none;
    box-shadow:0 0 8px rgba(124,58,237,0.2);
}

/* BUTTON */

.register-btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg,#7c3aed,#9333ea);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.register-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(124,58,237,0.35);
}

/* LOGIN */

.login-link{
    margin-top:20px;
    text-align:center;
    font-size:14px;
    color:#475569;
}

.login-link a{
    color:#7c3aed;
    text-decoration:none;
    font-weight:600;
}

@media(max-width:600px){

    .register-box{
        padding:25px;
    }

    .logo-section h2{
        font-size:26px;
    }
}

</style>

</head>

<body>

<div class="register-container">

    <div class="register-box">

        <!-- LOGO -->

        <div class="logo-section">

            <img
            src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
            class="logo"
            alt="Buyer"
            >

            <h2>Buyer Registration</h2>

            <p>Create your smart buyer account</p>

        </div>

        <!-- MESSAGE -->

        <?php if(!empty($message)){ ?>

            <div class="alert">
                <?php echo $message; ?>
            </div>

        <?php } ?>

        <!-- FORM -->

        <form method="POST">

            <div class="input-group">

                <label>Full Name</label>

                <div class="input-box">

                    <i class="fa fa-user"></i>

                    <input
                    type="text"
                    name="name"
                    placeholder="Enter full name"
                    required
                    >

                </div>

            </div>

            <div class="input-group">

                <label>Email Address</label>

                <div class="input-box">

                    <i class="fa fa-envelope"></i>

                    <input
                    type="email"
                    name="email"
                    placeholder="Enter email"
                    required
                    >

                </div>

            </div>

            <div class="input-group">

                <label>Mobile Number</label>

                <div class="input-box">

                    <i class="fa fa-phone"></i>

                    <input
                    type="text"
                    name="mobile_no"
                    placeholder="Enter mobile number"
                    required
                    >

                </div>

            </div>

            <div class="input-group">

                <label>Address</label>

                <div class="input-box">

                    <i class="fa fa-location-dot"></i>

                    <textarea
                    name="address"
                    placeholder="Enter address"
                    required
                    ></textarea>

                </div>

            </div>

            <div class="input-group">

                <label>Password</label>

                <div class="input-box">

                    <i class="fa fa-lock"></i>

                    <input
                    type="password"
                    name="password"
                    placeholder="Create password"
                    required
                    >

                </div>

            </div>

            <div class="input-group">

                <label>Confirm Password</label>

                <div class="input-box">

                    <i class="fa fa-shield"></i>

                    <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                    >

                </div>

            </div>

            <button type="submit" name="register" class="register-btn">
                Register Now
            </button>

        </form>

        <div class="login-link">

            Already have an account?

            <a href="buyer_login.php">
                Login Here
            </a>

        </div>

    </div>

</div>

</body>
</html>