<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_dashboard.php");
exit();
}

$farmer_id = $_SESSION['farmer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // 👇 THIS IS WHERE YOUR UPDATE QUERY GOES
    $sql = "UPDATE product 
            SET product_name='$product_name',
                price='$price',
                description='$description'
            WHERE product_id='$id' 
            AND farmer_id='$farmer_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Product updated successfully');
               window.location.href='farmer_dashboard.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>