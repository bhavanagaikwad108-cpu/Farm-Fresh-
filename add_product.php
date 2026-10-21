<?php

session_start();

include 'db.php';

/*
|--------------------------------------------------------------------------
| CHECK FARMER LOGIN
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['farmer_id']))
{
    header("Location: farmer_login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| MESSAGE VARIABLE
|--------------------------------------------------------------------------
*/

$message = "";

/*
|--------------------------------------------------------------------------
| ADD PRODUCT
|--------------------------------------------------------------------------
*/

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $farmer_id =
    $_SESSION['farmer_id'];

    $product_name =
    trim($_POST['product_name']);

    $category =
    trim($_POST['category']);

    $quantity =
    trim($_POST['quantity']);

    $price =
    trim($_POST['price']);

    $description =
    trim($_POST['description']);

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if(
        empty($product_name) ||
        empty($category) ||
        empty($quantity) ||
        empty($price)
    )
    {
        $message =
        "All required fields are mandatory!";
    }

    elseif(!is_numeric($quantity) || $quantity <= 0)
    {
        $message =
        "Quantity must be valid!";
    }

    elseif(!is_numeric($price) || $price <= 0)
    {
        $message =
        "Price must be valid!";
    }

    else
    {

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD
        |--------------------------------------------------------------------------
        */

        $image_name = "";

        if(
            isset($_FILES['image']) &&
            $_FILES['image']['error'] == 0
        )
        {

            $allowed_types =
            ['jpg','jpeg','png','webp'];

            $file_name =
            $_FILES['image']['name'];

            $file_tmp =
            $_FILES['image']['tmp_name'];

            $file_size =
            $_FILES['image']['size'];

            $file_ext =
            strtolower(
                pathinfo(
                    $file_name,
                    PATHINFO_EXTENSION
                )
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATE IMAGE TYPE
            |--------------------------------------------------------------------------
            */

            if(!in_array($file_ext, $allowed_types))
            {
                $message =
                "Only JPG, JPEG, PNG, WEBP allowed!";
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE SIZE
            |--------------------------------------------------------------------------
            */

            elseif($file_size > 2 * 1024 * 1024)
            {
                $message =
                "Image must be less than 2MB!";
            }

            else
            {

                /*
                |--------------------------------------------------------------------------
                | CREATE FOLDER IF NOT EXISTS
                |--------------------------------------------------------------------------
                */

                if(!is_dir("uploads/products"))
                {
                    mkdir(
                        "uploads/products",
                        0777,
                        true
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | RENAME IMAGE
                |--------------------------------------------------------------------------
                */

                $image_name =
                time() . "_" . $file_name;

                $upload_path =
                "uploads/products/" .
                $image_name;

                /*
                |--------------------------------------------------------------------------
                | MOVE FILE
                |--------------------------------------------------------------------------
                */

                if(
                    !move_uploaded_file(
                        $file_tmp,
                        $upload_path
                    )
                )
                {
                    $message =
                    "Image upload failed!";
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT PRODUCT
        |--------------------------------------------------------------------------
        */

        if(empty($message))
        {

            $query =
            "INSERT INTO product
            (
                farmer_id,
                product_name,
                category,
                quantity,
                price,
                description,
                image
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt =
            mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param(
                $stmt,
                "issidss",
                $farmer_id,
                $product_name,
                $category,
                $quantity,
                $price,
                $description,
                $image_name
            );

            if(mysqli_stmt_execute($stmt))
            {
                $message =
                "Product Added Successfully!";
            }

            else
            {
                $message =
                "Database Error!";
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
Add Product
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
>

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet"
>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    background:
    linear-gradient(
    135deg,
    #f3e8ff,
    #ede9fe,
    #faf5ff
    );

    font-family:'Poppins',sans-serif;

    min-height:100vh;
}

/* CONTAINER */

.main-container{

    max-width:850px;

    margin:50px auto;
}

/* CARD */

.card-box{

    background:white;

    padding:45px;

    border-radius:28px;

    box-shadow:
    0 15px 40px rgba(124,58,237,0.12);

    border:
    1px solid #ede9fe;
}

/* TITLE */

.card-box h2{

    font-weight:700;

    color:#581c87;

    margin-bottom:30px;
}

/* LABEL */

.form-label{

    font-weight:600;

    color:#4c1d95;

    margin-bottom:8px;
}

/* INPUTS */

.form-control,
.form-select{

    height:58px;

    border-radius:16px;

    border:1px solid #ddd6fe;

    padding-left:18px;

    font-size:15px;

    transition:0.3s;

    box-shadow:none;
}

.form-control:focus,
.form-select:focus{

    border-color:#7c3aed;

    box-shadow:
    0 0 0 4px rgba(124,58,237,0.12);
}

/* TEXTAREA */

textarea.form-control{

    height:130px;

    padding-top:15px;

    resize:none;
}

/* FILE INPUT */

input[type="file"]{

    padding:14px;
}

/* BUTTON */

.btn-add{

    height:58px;

    border:none;

    border-radius:18px;

    background:
    linear-gradient(
    135deg,
    #7c3aed,
    #9333ea
    );

    color:white;

    font-size:18px;

    font-weight:700;

    transition:0.3s;

    box-shadow:
    0 10px 25px rgba(124,58,237,0.25);
}

.btn-add:hover{

    transform:translateY(-3px);

    background:
    linear-gradient(
    135deg,
    #6d28d9,
    #7e22ce
    );

    box-shadow:
    0 15px 30px rgba(124,58,237,0.35);
}

/* ALERT */

.alert{

    border-radius:16px;

    padding:15px 20px;

    font-weight:500;
}

/* MOBILE */

@media(max-width:768px){

    .card-box{

        padding:25px;
    }

    .main-container{

        margin:20px auto;
    }
}

</style>

</head>

<body>

<div class="container main-container">

    <div class="card-box">

        <h2 class="text-center">

            <i class="fa-solid fa-seedling text-success"></i>

            Add Product

        </h2>

        <!-- MESSAGE -->

        <?php if(!empty($message)) { ?>

            <div class="alert alert-info">

                <?php echo $message; ?>

            </div>

        <?php } ?>

        <!-- FORM -->

        <form
        method="POST"
        enctype="multipart/form-data"
        >

            <!-- PRODUCT NAME -->

            <div class="mb-3">

                <label class="form-label">
                    Product Name
                </label>

                <input
                type="text"
                name="product_name"
                class="form-control"
                placeholder="Enter product name"
                required
                >

            </div>

            <!-- CATEGORY -->

            <div class="mb-3">

                <label class="form-label">
                    Category
                </label>

                <select
                name="category"
                class="form-select"
                required
                >

                    <option value="">
                        Select Category
                    </option>

                    <option value="Vegetables">
                        Vegetables
                    </option>

                    <option value="Fruits">
                        Fruits
                    </option>

                    <option value="Grains">
                        Grains
                    </option>

                    <option value="Organic">
                        Organic
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>

            <!-- QUANTITY -->

            <div class="mb-3">

                <label class="form-label">
                    Quantity
                </label>

                <input
                type="number"
                name="quantity"
                class="form-control"
                placeholder="Enter quantity in kg"
                required
                >

            </div>

            <!-- PRICE -->

            <div class="mb-3">

                <label class="form-label">
                    Price
                </label>

                <input
                type="number"
                step="0.01"
                name="price"
                class="form-control"
                placeholder="Enter price per kg"
                required
                >

            </div>

            <!-- DESCRIPTION -->

            <div class="mb-3">

                <label class="form-label">
                    Description
                </label>

                <textarea
                name="description"
                class="form-control"
                placeholder="Enter product description"
                ></textarea>

            </div>

            <!-- IMAGE -->

            <div class="mb-4">

                <label class="form-label">
                    Product Image
                </label>

                <input
                type="file"
                name="image"
                class="form-control"
                accept="image/*"
                >

            </div>

            <!-- BUTTON -->

            <button
            type="submit"
            class="btn-add w-100"
            >

                <i class="fa-solid fa-plus"></i>

                Add Product

            </button>

            
            <h5 class=text center >
            <a href="farmer_dashboard.php" class="back-btn">
            <i class="fa fa-arrow-left"></i>
            Back Dashboard 
        </a>
            </h5>

        </form>

    </div>

</div>

</body>
</html>