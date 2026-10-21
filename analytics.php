<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

/* TOTAL ORDERS */

$total_orders_query = mysqli_query($conn,"
SELECT COUNT(*) AS total_orders
FROM orders
WHERE farmer_id='$farmer_id'
");

$total_orders = mysqli_fetch_assoc($total_orders_query)['total_orders'];

/* PENDING ORDERS */

$pending_query = mysqli_query($conn,"
SELECT COUNT(*) AS pending_orders
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Pending'
");

$pending_orders = mysqli_fetch_assoc($pending_query)['pending_orders'];

/* DELIVERED ORDERS */

$delivered_query = mysqli_query($conn,"
SELECT COUNT(*) AS delivered_orders
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Delivered'
");

$delivered_orders = mysqli_fetch_assoc($delivered_query)['delivered_orders'];

/* TOTAL PRODUCTS */

$product_query = mysqli_query($conn,"
SELECT COUNT(*) AS total_products
FROM product
WHERE farmer_id='$farmer_id'
");

$total_products = mysqli_fetch_assoc($product_query)['total_products'];

/* LOW STOCK PRODUCTS */

$low_stock = mysqli_query($conn,"
SELECT *
FROM product
WHERE farmer_id='$farmer_id'
AND quantity < 10
");

/* OUT OF STOCK */

$out_stock = mysqli_query($conn,"
SELECT *
FROM product
WHERE farmer_id='$farmer_id'
AND quantity <= 0
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Analytics</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
    padding:30px;
}

.title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.title h2{
    font-size:32px;
    font-weight:700;
}

.back-btn{
    background:#22c55e;
    color:white;
    padding:12px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
}

.card-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    position:relative;
}

.card-box i{
    position:absolute;
    top:20px;
    right:20px;
    font-size:50px;
    opacity:0.1;
}

.card-box h2{
    font-size:35px;
    font-weight:700;
}

.card-box p{
    color:#64748b;
}

.alert-box{
    background:white;
    padding:25px;
    border-radius:20px;
    margin-top:25px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
}

.alert-box h3{
    margin-bottom:20px;
    font-size:24px;
}

.alert-item{
    padding:12px;
    border-radius:12px;
    margin-bottom:10px;
    font-weight:600;
}

.low{
    background:#fef3c7;
    color:#92400e;
}

.out{
    background:#fee2e2;
    color:#991b1b;
}

</style>

</head>

<body>

<div class="title">

    <h2>📊 Analytics</h2>

    <a href="farmer_dashboard.php" class="back-btn">
        Back Dashboard
    </a>

</div>

<div class="cards">

    <div class="card-box">
        <i class="fa fa-cart-shopping"></i>
        <h2><?php echo $total_orders; ?></h2>
        <p>Total Orders</p>
    </div>

    <div class="card-box">
        <i class="fa fa-clock"></i>
        <h2><?php echo $pending_orders; ?></h2>
        <p>Pending Orders</p>
    </div>

    <div class="card-box">
        <i class="fa fa-check-circle"></i>
        <h2><?php echo $delivered_orders; ?></h2>
        <p>Delivered Orders</p>
    </div>

    <div class="card-box">
        <i class="fa fa-box"></i>
        <h2><?php echo $total_products; ?></h2>
        <p>Total Products</p>
    </div>

</div>

<div class="alert-box">

    <h3>⚠ Low Stock Products</h3>

    <?php while($row = mysqli_fetch_assoc($low_stock)) { ?>

        <div class="alert-item low">
            <?php echo $row['product_name']; ?> :
            <?php echo $row['quantity']; ?> KG Left
        </div>

    <?php } ?>

</div>

<div class="alert-box">

    <h3>❌ Out Of Stock Products</h3>

    <?php while($row = mysqli_fetch_assoc($out_stock)) { ?>

        <div class="alert-item out">
            <?php echo $row['product_name']; ?> is Out Of Stock
        </div>

    <?php } ?>

</div>

</body>
</html>