<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'db.php';

$message = "";

/*
|--------------------------------------------------------------------------
| Login Process
|--------------------------------------------------------------------------
*/

if(isset($_POST['login']))
{
    // Get Form Data
    $email =
    trim($_POST['email']);

    $password =
    trim($_POST['password']);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if(empty($email) || empty($password))
    {
        $message =
        "All fields are required!";
    }

    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $message =
        "Invalid email format!";
    }

    else
    {
        /*
        |--------------------------------------------------------------------------
        | Check Farmer Email
        |--------------------------------------------------------------------------
        */

        $query =
        "SELECT * FROM farmer WHERE email = ?";

        $stmt =
        mysqli_prepare($conn, $query);

        if(!$stmt)
        {
            die(
                "Query Failed: " .
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result =
        mysqli_stmt_get_result($stmt);

        /*
        |--------------------------------------------------------------------------
        | Farmer Found
        |--------------------------------------------------------------------------
        */

        if(mysqli_num_rows($result) == 1)
        {
            $row =
            mysqli_fetch_assoc($result);

            /*
            |--------------------------------------------------------------------------
            | Verify Password
            |--------------------------------------------------------------------------
            */

            if(
                password_verify(
                    $password,
                    $row['password']
                )
            )
            {
                /*
                |--------------------------------------------------------------------------
                | Secure Session
                |--------------------------------------------------------------------------
                */

                session_regenerate_id(true);

                /*
                |--------------------------------------------------------------------------
                | Store Session
                |--------------------------------------------------------------------------
                */

                $_SESSION['farmer_id'] =
                $row['farmer_id'];

                $_SESSION['farmer_name'] =
                $row['full_name'];

                $_SESSION['farmer_email'] =
                $row['email'];

                $_SESSION['profile_image'] =
                $row['profile_image'];

                /*
                |--------------------------------------------------------------------------
                | Redirect Dashboard
                |--------------------------------------------------------------------------
                */

                header(
                    "Location: farmer_dashboard.php"
                );

                exit();

            }
            else
            {
                $message =
                "Incorrect password!";
            }

        }
        else
        {
            $message =
            "Email not registered!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"
>

<title>
Farmer Login
</title>

<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<!-- Font Awesome -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
>

<!-- Google Font -->

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet"
>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{

    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #1e293b,
        #111827
    );

    display:flex;
    justify-content:center;
    align-items:center;

    padding:30px;
}

/* Login Card */

.login-card{

    width:100%;
    max-width:450px;

    background:white;

    border-radius:30px;

    overflow:hidden;

    box-shadow:
    0 20px 60px rgba(0,0,0,0.35);

    animation:fadeIn 0.7s ease;
}

@keyframes fadeIn{

    from{
        opacity:0;
        transform:translateY(25px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* Top Section */

.top-section{

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
    );

    padding:45px;

    text-align:center;

    color:white;
}

.top-section i{

    font-size:65px;

    margin-bottom:15px;
}

.top-section h2{

    font-size:34px;

    font-weight:700;
}

.top-section p{

    margin-top:10px;

    color:#dbeafe;
}

/* Form Section */

.form-section{

    padding:40px;
}

.form-label{

    font-weight:600;

    color:#334155;

    margin-bottom:8px;
}

.form-control{

    height:55px;

    border-radius:14px;

    border:1px solid #dbeafe;

    padding-left:15px;

    transition:0.3s;
}

.form-control:focus{

    border-color:#2563eb;

    box-shadow:
    0 0 0 0.2rem rgba(37,99,235,0.15);
}

/* Password */

.password-box{

    position:relative;
}

.password-box i{

    position:absolute;

    right:18px;
    top:18px;

    color:#64748b;

    cursor:pointer;
}

/* Button */

.btn-login{

    height:58px;

    border:none;

    border-radius:16px;

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
    );

    color:white;

    font-size:18px;

    font-weight:700;

    transition:0.3s;
}

.btn-login:hover{

    transform:translateY(-3px);

    box-shadow:
    0 10px 25px rgba(37,99,235,0.3);
}

/* Bottom Link */

.bottom-link{

    text-align:center;

    margin-top:25px;

    color:#475569;
}

.bottom-link a{

    text-decoration:none;

    color:#2563eb;

    font-weight:700;
}

/* Responsive */

@media(max-width:500px){

    .top-section{

        padding:35px;
    }

    .form-section{

        padding:25px;
    }

    .top-section h2{

        font-size:28px;
    }
}

</style>

</head>

<body>

<div class="login-card">

    <!-- Top Section -->

    <div class="top-section">

        <i class="fa-solid fa-seedling"></i>

        <h2>
            Farmer Login
        </h2>

        <p>
            Access your smart farming dashboard
        </p>

    </div>

    <!-- Form Section -->

    <div class="form-section">

        <!-- Error Message -->

        <?php if(!empty($message)) { ?>

            <div class="alert alert-danger">

                <?php echo $message; ?>

            </div>

        <?php } ?>

        <form method="POST">

            <!-- Email -->

            <div class="mb-3">

                <label class="form-label">
                    Email Address
                </label>

                <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter your email"
                required
                >

            </div>

            <!-- Password -->

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <div class="password-box">

                    <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required
                    >

                    <i
                    class="fa-solid fa-eye"
                    onclick="togglePassword()"
                    ></i>

                </div>

            </div>

            <!-- Login Button -->

            <button
            type="submit"
            name="login"
            class="btn btn-login w-100"
            >

                <i class="fa-solid fa-right-to-bracket"></i>

                Login

            </button>

        </form>

        <!-- Register -->

        <div class="bottom-link">

            Don't have an account?

            <a href="farmer_register.php">
                Register Here
            </a>

        </div>

    </div>

</div>

<script>

function togglePassword(){

    let password =
    document.getElementById("password");

    if(password.type === "password")
    {
        password.type = "text";
    }
    else
    {
        password.type = "password";
    }
}

</script>

</body>
</html>