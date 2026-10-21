<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

// check id from URL
if (!isset($_GET['id'])) {
    die("Product ID not found");
}

$product_id = $_GET['id'];

// fetch product data (ONLY current farmer's product)
$sql = "SELECT * FROM product 
        WHERE product_id='$product_id' 
        AND farmer_id='$farmer_id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Product not found or unauthorized access");
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        button {
            background: green;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        button:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Edit Product</h2>

    <form action="update_product.php" method="POST">

        <!-- hidden id -->
        <input type="hidden" name="id" value="<?php echo $row['product_id']; ?>">

        <label>Product Name</label>
        <input type="text" name="product_name" 
               value="<?php echo $row['product_name']; ?>" required>

        <label>Price</label>
        <input type="text" name="price" 
               value="<?php echo $row['price']; ?>" required>

        <label>Description</label>
        <textarea name="description" required><?php echo $row['description']; ?></textarea>

        <button type="submit">Update Product</button>

    </form>

</div>

</body>
</html>