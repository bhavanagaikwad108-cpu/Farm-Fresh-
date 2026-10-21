<?php

session_start();
include("db.php");

/* CHECK DELIVERY LOGIN */

if(!isset($_SESSION['delivery_boy_id']))
{
    header("Location: delivery_login.php");
    exit();
}

/* GET DATA */

$order_id = $_GET['order_id'];
$status = $_GET['status'];

/* UPDATE ORDER STATUS */

mysqli_query($conn,"
UPDATE orders
SET order_status='$status'
WHERE order_id='$order_id'
");
if($status == 'Delivered')
{
    mysqli_query($conn,"
    UPDATE orders
    SET payment_status='Paid'
    WHERE order_id='$order_id'
    ");
}
/* REDIRECT */

header("Location: delivery_dashboard.php");
exit();

?>