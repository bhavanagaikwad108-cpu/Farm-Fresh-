<?php
session_start();
include("db.php");

if(!isset($_SESSION['buyer_id']))
{
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

$product_id = intval($_POST['product_id']);

$check = mysqli_query($conn,"
SELECT *
FROM wishlist
WHERE buyer_id='$buyer_id'
AND product_id='$product_id'
");

if(mysqli_num_rows($check) > 0)
{
    mysqli_query($conn,"
    DELETE FROM wishlist
    WHERE buyer_id='$buyer_id'
    AND product_id='$product_id'
    ");

    echo "removed";
}
else
{
    mysqli_query($conn,"
    INSERT INTO wishlist(buyer_id,product_id)
    VALUES('$buyer_id','$product_id')
    ");

    echo "added";
}
?>