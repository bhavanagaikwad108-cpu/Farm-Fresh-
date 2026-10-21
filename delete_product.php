<?php
session_start();
include("db.php");

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $delete_query = "DELETE FROM product 
                     WHERE product_id='$product_id' 
                     AND farmer_id='$farmer_id'";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: farmer_dashboard.php");
        exit();
    } else {
        echo "Delete Failed!";
    }
} else {
    echo "Invalid Request!";
}
?>