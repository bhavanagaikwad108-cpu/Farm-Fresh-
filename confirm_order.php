<?php
session_start();
include("db.php");

if(isset($_POST['product_id']) && isset($_POST['quantity']))
{
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $buyer_id = $_SESSION['buyer_id'];

    // Insert into orders table
    $query = "INSERT INTO orders (buyer_id, product_id, quantity)
              VALUES ('$buyer_id', '$product_id', '$quantity')";

    if(mysqli_query($conn, $query))
    {
        echo "Order placed successfully!";
    }
    else
    {
        echo "Error: " . mysqli_error($conn);
    }
}
?>