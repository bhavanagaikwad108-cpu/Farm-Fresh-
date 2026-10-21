<?php
session_start();
include("db.php");

/* LOGIN CHECK */

if(!isset($_SESSION['buyer_id']))
{
    header("Location: buyer_login.php");
    exit();
}

/* PLACE ORDER */

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $buyer_id = $_SESSION['buyer_id'];

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    /* GET PRODUCT */

    $product_query = mysqli_query($conn,"
    SELECT *
    FROM product
    WHERE product_id='$product_id'
    ");

    if(mysqli_num_rows($product_query) == 0)
    {
        die("Product not found");
    }

    $product = mysqli_fetch_assoc($product_query);

    $farmer_id = $product['farmer_id'];
    $price = $product['price'];
    $stock = $product['quantity'];

    /* CHECK STOCK */

    if($quantity > $stock)
    {
        die("Not enough stock available");
    }

    /* TOTAL */

    $total_price = $price * $quantity;

    /* INSERT ORDER */

    $insert = mysqli_query($conn,"
INSERT INTO orders
(
    buyer_id,
    farmer_id,
    product_id,
    quantity,
    total_price,
    order_status,
    payment_method,
    payment_status
)

VALUES
(
    '$buyer_id',
    '$farmer_id',
    '$product_id',
    '$quantity',
    '$total_price',
    'Pending',
    'Cash On Delivery',
    'Pending'
)
    ");

    if($insert)
    {
        /* UPDATE PRODUCT STOCK */

        $new_stock = $stock - $quantity;

        mysqli_query($conn,"
        UPDATE product
        SET quantity='$new_stock'
        WHERE product_id='$product_id'
        ");

        echo "
        <script>
        alert('Order placed successfully');
        window.location='buyer_dashboard.php';
        </script>
        ";
    }
    else
    {
        echo mysqli_error($conn);
    }
}
?>