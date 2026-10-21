<?php
session_start();
include("db.php");

if(!isset($_SESSION['buyer_id']))
{
    header("Location: buyer_login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

if(isset($_GET['order_id']))
{
    $order_id = intval($_GET['order_id']);

    /* CHECK ORDER */

    $order_query = mysqli_query($conn,"
    SELECT *
    FROM orders
    WHERE order_id='$order_id'
    AND buyer_id='$buyer_id'
    ");

    if(mysqli_num_rows($order_query) > 0)
    {
        $order = mysqli_fetch_assoc($order_query);

        /* ONLY PENDING */

        if($order['order_status'] == 'Pending')
        {
            $product_id = $order['product_id'];
            $quantity = $order['quantity'];

            /* RETURN PRODUCT STOCK */

            mysqli_query($conn,"
            UPDATE product
            SET quantity = quantity + '$quantity'
            WHERE product_id='$product_id'
            ");

            /* DELETE ORDER */

            mysqli_query($conn,"
            DELETE FROM orders
            WHERE order_id='$order_id'
            ");

            header("Location: buyer_orders.php");
            exit();
        }
    }
}

header("Location: buyer_orders.php");
?>