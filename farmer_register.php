<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = trim($_POST['password']);
    $farm_address = trim($_POST['farm_address']);
    $crop_type = trim($_POST['crop_type']);

    // Validation
    if (
        empty($full_name) ||
        empty($email) ||
        empty($mobile) ||
        empty($password)
    ) {

        $message = "All required fields are mandatory!";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email format!";
    }

    elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {

        $message = "Mobile number must be 10 digits!";
    }

    elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters!";
    }

    else {

        // Check existing email
        $checkQuery =
        "SELECT * FROM farmer WHERE email = ?";

        $stmt =
        mysqli_prepare($conn, $checkQuery);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result =
        mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $message =
            "Email already registered!";
        }

        else {

            // Hash password
            $hashed_password =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Image Upload
            $profile_image = "";

            if (
                isset($_FILES['profile_image']) &&
                $_FILES['profile_image']['error'] == 0
            ) {

                $allowed_types =
                ['jpg','jpeg','png','webp'];

                $file_name =
                $_FILES['profile_image']['name'];

                $file_tmp =
                $_FILES['profile_image']['tmp_name'];

                $file_size =
                $_FILES['profile_image']['size'];

                $file_ext =
                strtolower(
                    pathinfo(
                        $file_name,
                        PATHINFO_EXTENSION
                    )
                );

                if (
                    in_array(
                        $file_ext,
                        $allowed_types
                    )
                ) {

                    if (
                        $file_size <= 2 * 1024 * 1024
                    ) {

                        // Create folder if not exists
                        if (
                            !is_dir("uploads/farmers")
                        ) {

                            mkdir(
                                "uploads/farmers",
                                0777,
                                true
                            );
                        }

                        $new_file_name =
                        time() . "_" . $file_name;

                        $upload_path =
                        "uploads/farmers/" .
                        $new_file_name;

                        if (
                            move_uploaded_file(
                                $file_tmp,
                                $upload_path
                            )
                        ) {

                            $profile_image =
                            $new_file_name;
                        }

                        else {

                            $message =
                            "Image upload failed!";
                        }

                    }

                    else {

                        $message =
                        "Image must be less than 2MB!";
                    }

                }

                else {

                    $message =
                    "Only JPG, JPEG, PNG, WEBP files allowed!";
                }
            }

            // Insert Data
            if (empty($message)) {

                $insertQuery = "
                INSERT INTO farmer
                (
                    full_name,
                    email,
                    mobile,
                    password,
                    farm_address,
                    crop_type,
                    profile_image
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ";

                $stmt =
                mysqli_prepare(
                    $conn,
                    $insertQuery
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssss",
                    $full_name,
                    $email,
                    $mobile,
                    $hashed_password,
                    $farm_address,
                    $crop_type,
                    $profile_image
                );

                if (
                    mysqli_stmt_execute($stmt)
                ) {

                    // IMPORTANT FIX
                    session_regenerate_id(true);

                    $_SESSION['farmer_id'] =
                    mysqli_insert_id($conn);

                    $_SESSION['farmer_email'] =
                    $email;

                    $_SESSION['farmer_name'] =
                    $full_name;

                    header(
                        "Location: farmer_dashboard.php"
                    );

                    exit();
                }

                else {

                    $message =
                    "Registration failed!";
                }
            }
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
Smart Farming Registration
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

    padding:40px;
}

/* Main Card */

.register-card{

    width:100%;
    max-width:780px;

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

/* Top Banner */

.top-banner{

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
    );

    padding:45px;

    color:white;

    text-align:center;
}

.top-banner i{

    font-size:65px;

    margin-bottom:15px;
}

.top-banner h2{

    font-size:36px;

    font-weight:700;
}

.top-banner p{

    margin-top:10px;

    color:#dbeafe;
}

/* Form Area */

.form-area{

    padding:45px;
}

/* Section Title */

.section-title{

    font-size:18px;

    font-weight:700;

    color:#1e293b;

    margin-bottom:20px;

    border-left:5px solid #2563eb;

    padding-left:12px;
}

/* Labels */

.form-label{

    font-weight:600;

    color:#334155;

    margin-bottom:8px;
}

/* Inputs */

.form-control,
.form-select{

    height:55px;

    border-radius:14px;

    border:1px solid #dbeafe;

    padding-left:15px;

    transition:0.3s;
}

textarea.form-control{

    height:110px;

    padding-top:15px;
}

.form-control:focus,
.form-select:focus{

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
    top:19px;

    color:#64748b;

    cursor:pointer;
}

/* Upload */

.upload-box{

    border:2px dashed #cbd5e1;

    border-radius:18px;

    padding:25px;

    text-align:center;

    transition:0.3s;
}

.upload-box:hover{

    border-color:#2563eb;

    background:#f8fafc;
}

/* Preview */

.preview-img{

    width:100px;
    height:100px;

    object-fit:cover;

    border-radius:50%;

    margin-top:15px;

    border:4px solid #dbeafe;

    display:none;
}

/* Button */

.btn-register{

    height:60px;

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

.btn-register:hover{

    transform:translateY(-3px);

    box-shadow:
    0 10px 25px rgba(37,99,235,0.3);
}

/* Features */

.feature-area{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(180px,1fr));

    gap:15px;

    margin-top:35px;
}

.feature-box{

    background:#f8fafc;

    border-radius:18px;

    padding:20px;

    text-align:center;

    transition:0.3s;
}

.feature-box:hover{

    transform:translateY(-5px);

    background:#eff6ff;
}

.feature-box i{

    font-size:28px;

    color:#2563eb;

    margin-bottom:10px;
}

.feature-box h6{

    font-weight:700;
}

/* Login */

.login-link{

    text-align:center;

    margin-top:30px;

    color:#475569;
}

.login-link a{

    text-decoration:none;

    color:#2563eb;

    font-weight:700;
}

/* Responsive */

@media(max-width:768px){

    .top-banner{

        padding:30px;
    }

    .form-area{

        padding:25px;
    }

    .top-banner h2{

        font-size:28px;
    }
}

</style>

</head>

<body>

<div class="register-card">

    <!-- Banner -->
    <div class="top-banner">

        <i class="fa-solid fa-seedling"></i>

        <h2>
            Smart Farming System
        </h2>

        <p>
            Professional Agriculture Management Platform
        </p>

    </div>

    <!-- Form Area -->
    <div class="form-area">

        <!-- Error -->
        <?php if(!empty($message)) { ?>

            <div class="alert alert-danger">

                <?php echo $message; ?>

            </div>

        <?php } ?>

        <form
        method="POST"
        enctype="multipart/form-data"
        >

            <!-- Personal -->
            <div class="section-title">
                Personal Information
            </div>

            <!-- Name -->
            <div class="mb-3">

                <label class="form-label">
                    Full Name
                </label>

                <input
                type="text"
                name="full_name"
                class="form-control"
                placeholder="Enter full name"
                required
                >

            </div>

            <!-- Email -->
            <div class="mb-3">

                <label class="form-label">
                    Email Address
                </label>

                <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter email address"
                required
                >

            </div>

            <!-- Mobile -->
            <div class="mb-3">

                <label class="form-label">
                    Mobile Number
                </label>

                <input
                type="text"
                name="mobile"
                class="form-control"
                placeholder="Enter mobile number"
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
                    placeholder="Create password"
                    required
                    >

                    <i
                    class="fa-solid fa-eye"
                    onclick="togglePassword()"
                    ></i>

                </div>

            </div>

            <!-- Farm -->
            <div class="section-title">
                Farm Information
            </div>

            <!-- Address -->
            <div class="mb-3">

                <label class="form-label">
                    Farm Address
                </label>

                <textarea
                name="farm_address"
                class="form-control"
                placeholder="Enter farm address"
                ></textarea>

            </div>

            <!-- Crop -->
            <div class="mb-3">

                <label class="form-label">
                    Crop Category
                </label>

                <select
                name="crop_type"
                class="form-select"
                required
                >

                    <option value="">
                        Select Crop Category
                    </option>

                    <option value="Cereal Crops">
                        Cereal Crops
                    </option>

                    <option value="Pulse Crops">
                        Pulse Crops
                    </option>

                    <option value="Oilseed Crops">
                        Oilseed Crops
                    </option>

                    <option value="Cash Crops">
                        Cash Crops
                    </option>

                    <option value="Fiber Crops">
                        Fiber Crops
                    </option>

                    <option value="Vegetable Crops">
                        Vegetable Crops
                    </option>

                    <option value="Fruit Crops">
                        Fruit Crops
                    </option>

                    <option value="Organic Farming">
                        Organic Farming
                    </option>

                </select>

            </div>

            <!-- Upload -->
            <div class="mb-4">

                <label class="form-label">
                    Profile Image
                </label>

                <div class="upload-box">

                    <input
                    type="file"
                    name="profile_image"
                    class="form-control"
                    accept="image/*"
                    onchange="previewImage(event)"
                    >

                    <img
                    id="preview"
                    class="preview-img"
                    >

                </div>

            </div>

            <!-- Button -->
            <button
            type="submit"
            class="btn btn-register w-100"
            >

                <i class="fa-solid fa-user-plus"></i>

                Create Farmer Account

            </button>

        </form>

        <!-- Features -->
        <div class="feature-area">

            <div class="feature-box">

                <i class="fa-solid fa-chart-line"></i>

                <h6>
                    Smart Analytics
                </h6>

                <p>
                    Monitor crops and farming activity
                </p>

            </div>

            <div class="feature-box">

                <i class="fa-solid fa-truck"></i>

                <h6>
                    Delivery Support
                </h6>

                <p>
                    Manage delivery and logistics
                </p>

            </div>

            <div class="feature-box">

                <i class="fa-solid fa-shield-halved"></i>

                <h6>
                    Secure Platform
                </h6>

                <p>
                    Protected farmer data and access
                </p>

            </div>

        </div>

        <!-- Login -->
        <div class="login-link">

            Already have an account?

            <a href="farmer_login.php">
                Login Here
            </a>

        </div>

    </div>

</div>

<script>

function togglePassword(){

    let password =
    document.getElementById("password");

    if(password.type === "password"){

        password.type = "text";
    }

    else{

        password.type = "password";
    }
}

function previewImage(event){

    let preview =
    document.getElementById("preview");

    preview.src =
    URL.createObjectURL(
        event.target.files[0]
    );

    preview.style.display = "block";
}

</script>

</body>
</html>