<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['status'])){

    $order_id = intval($_GET['id']);
    $status = trim($_GET['status']);

    /* GET ORDER DETAILS */

    $order_query = mysqli_query($conn,"
    SELECT * FROM orders
    WHERE order_id='$order_id'
    ");

    $order = mysqli_fetch_assoc($order_query);

    if($order){

        $product_id = $order['product_id'];
        $order_quantity = (int)$order['quantity'];
        $current_status = trim($order['order_status']);

        /*
        REDUCE STOCK ONLY ONCE
        WHEN PENDING → ACCEPTED
        */

        if(
            strtolower($status) == 'accepted'
            &&
            strtolower($current_status) == 'pending'
        ){

            /* GET PRODUCT STOCK */

            $product_query = mysqli_query($conn,"
            SELECT quantity
            FROM product
            WHERE product_id='$product_id'
            ");

            $product = mysqli_fetch_assoc($product_query);

            if($product){

                $current_quantity = (int)$product['quantity'];

                /* NEW STOCK */

                $new_quantity = $current_quantity - $order_quantity;

                if($new_quantity < 0){
                    $new_quantity = 0;
                }

                /* UPDATE STOCK */

                mysqli_query($conn,"
                UPDATE product
                SET quantity='$new_quantity'
                WHERE product_id='$product_id'
                ");
            }
        }

        /* UPDATE ORDER STATUS */

        mysqli_query($conn,"
        UPDATE orders
        SET order_status='$status'
        WHERE order_id='$order_id'
        ");
    }

    header("Location: my_order.php");
    exit();
}
?>