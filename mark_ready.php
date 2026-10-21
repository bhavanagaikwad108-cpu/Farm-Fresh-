<?php

session_start();

include 'db.php';

/* LOGIN CHECK */

if(!isset($_SESSION['farmer_id']))
{
    header("Location: farmer_login.php");
    exit();
}

/* CHECK ORDER ID */

if(isset($_GET['order_id']))
{
    $order_id = intval($_GET['order_id']);

    /* UPDATE STATUS */

    $update = mysqli_query($conn, "

    UPDATE orders
    SET order_status='Ready For Pickup'
    WHERE order_id='$order_id'

    ");

    if($update)
    {
        header("Location: my_order.php?success=ready");
        exit();
    }
    else
    {
        echo 'Failed To Update Status';
    }
}
else
{
    echo 'Invalid Order';
}

?>