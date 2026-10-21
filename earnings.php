<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

/* TOTAL EARNINGS */

$total_earnings_query = mysqli_query($conn,"
SELECT SUM(total_price) AS total_earnings
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Delivered'
");

$total_earnings = mysqli_fetch_assoc($total_earnings_query)['total_earnings'];

if(!$total_earnings){
    $total_earnings = 0;
}

/* MONTHLY EARNINGS */

$monthly_query = mysqli_query($conn,"
SELECT SUM(total_price) AS monthly_earnings
FROM orders
WHERE farmer_id='$farmer_id'
AND MONTH(order_date)=MONTH(CURRENT_DATE())
AND YEAR(order_date)=YEAR(CURRENT_DATE())
AND order_status='Delivered'
");

$monthly_earnings = mysqli_fetch_assoc($monthly_query)['monthly_earnings'];

if(!$monthly_earnings){
    $monthly_earnings = 0;
}

/* PENDING PAYMENTS */

$pending_query = mysqli_query($conn,"
SELECT SUM(total_price) AS pending_payment
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Accepted'
");

$pending_payment = mysqli_fetch_assoc($pending_query)['pending_payment'];

if(!$pending_payment){
    $pending_payment = 0;
}

/* DELIVERED ORDERS */

$delivered_query = mysqli_query($conn,"
SELECT COUNT(*) AS delivered_orders
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Delivered'
");

$delivered_orders = mysqli_fetch_assoc($delivered_query)['delivered_orders'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Earnings</title>

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
    margin-bottom:30

}

.title h2{
    font-size:32px;
    font-weight:700;
}

.back-btn{
    background:blueviolet;
    color:white;
    padding:12px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
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
    opacity:0.3;
    color:#22c55e;
}

.card-box h2{
    font-size:34px;
    font-weight:700;
}

.card-box p{
    color:#64748b;
}
.card-box:hover{
    transform:translateY(-6px);
}

.green{
    border-left:5px solid #22c55e;
}

.blue{
    border-left:5px solid #3b82f6;
}

.orange{
    border-left:5px solid #f59e0b;
}

.purple{
    border-left:5px solid #8b5cf6;
}

</style>

</head>

<body>

<div class="title">

    <h2>💰 Earnings</h2>

    <a href="farmer_dashboard.php" class="back-btn">
        Back Dashboard
    </a>

</div>

<div class="cards">

    <div class="card-box green">
        <i class="fa fa-wallet"></i>
        <h2>₹<?php echo number_format($total_earnings); ?></h2>
        <p>Total Earnings</p>
    </div>

    <div class="card-box blue">
        <i class="fa fa-calendar"></i>
        <h2>₹<?php echo number_format($monthly_earnings); ?></h2>
        <p>This Month Earnings</p>
    </div>

    <div class="card-box orange">
        <i class="fa fa-clock"></i>
        <h2>₹<?php echo number_format($pending_payment); ?></h2>
        <p>Pending Payments</p>
    </div>

    <div class="card-box purple">
        <i class="fa fa-check-circle"></i>
        <h2><?php echo $delivered_orders; ?></h2>
        <p>Delivered Orders</p>
    </div>

</div>

</body>
</html>