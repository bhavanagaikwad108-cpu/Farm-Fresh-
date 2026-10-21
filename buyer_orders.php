<?php
session_start();
include("db.php");

/* LOGIN CHECK */

if(!isset($_SESSION['buyer_id']))
{
    header("Location: buyer_login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

/* BUYER DETAILS */

$buyer_query = mysqli_query($conn,"
SELECT *
FROM buyer
WHERE buyer_id='$buyer_id'
");

$buyer = mysqli_fetch_assoc($buyer_query);

/* ORDERS */

$orders = mysqli_query($conn,"
SELECT
orders.*,
product.product_name,
product.image,
farmer.full_name
FROM orders
JOIN product
ON orders.product_id = product.product_id
JOIN farmer
ON orders.farmer_id = farmer.farmer_id
WHERE orders.buyer_id='$buyer_id'
ORDER BY orders.order_id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>My Orders</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    background:#f1f5f9;
}

/* SIDEBAR */

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#581c87,#7e22ce);
    padding:25px 18px;
    color:white;
}

.logo{
    text-align:center;
    margin-bottom:35px;
}

.logo h2{
    font-size:25px;
    font-weight:700;
}

.logo p{
    color:#e9d5ff;
    font-size:13px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    text-decoration:none;
    color:white;
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:12px;
    transition:0.3s;
}

.menu a:hover,
.active{
    background:rgba(255,255,255,0.15);
    transform:translateX(5px);
}

/* MAIN */

.main{
    margin-left:260px;
    padding:25px;
}

/* HEADER */

.header{
    background:white;
    padding:22px;
    border-radius:22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

.header h2{
    font-weight:700;
}

.profile{
    background:#faf5ff;
    padding:10px 18px;
    border-radius:50px;
}

/* ORDER SECTION */

.section-title{
    margin-top:35px;
    margin-bottom:20px;
    font-size:28px;
    font-weight:700;
}

.orders-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(340px,1fr));
    gap:25px;
}

/* CARD */

.order-card{
    background:white;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    transition:0.3s;
}

.order-card:hover{
    transform:translateY(-6px);
}

.order-image{
    width:100%;
    height:230px;
    overflow:hidden;
}

.order-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.4s;
}

.order-card:hover img{
    transform:scale(1.05);
}

.order-content{
    padding:20px;
}

.order-content h3{
    font-size:24px;
    font-weight:700;
    margin-bottom:15px;
}

.info{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:12px;
    color:#475569;
    font-size:15px;
}

.info i{
    color:#7e22ce;
    width:20px;
}

.price{
    font-size:28px;
    font-weight:700;
    color:#7e22ce;
    margin-top:10px;
}

/* STATUS */

.status{
    display:inline-block;
    margin-top:18px;
    padding:8px 18px;
    border-radius:40px;
    font-size:13px;
    font-weight:600;
}

.pending{
    background:#fef3c7;
    color:#92400e;
}

.accepted{
    background:#dbeafe;
    color:#1d4ed8;
}

.ready{
    background:#ede9fe;
    color:#6d28d9;
}

.picked{
    background:#fef9c3;
    color:#854d0e;
}

.out{
    background:#dbeafe;
    color:#1d4ed8;
}

.delivered{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

/* EMPTY */

.empty-box{
    margin-top:40px;
    background:white;
    padding:70px 20px;
    border-radius:24px;
    text-align:center;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

.empty-box i{
    font-size:80px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-box h3{
    font-weight:700;
}

.empty-box p{
    color:#64748b;
}

/* MOBILE */

@media(max-width:900px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main{
        margin-left:0;
    }

    .header{
        flex-direction:column;
        gap:15px;
    }
}

.cancel-btn{
    display:inline-block;
    margin-top:18px;
    padding:12px 18px;
    background:#ef4444;
    color:white;
    text-decoration:none;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    transition:0.3s;
}

.cancel-btn:hover{
    background:#dc2626;
    color:white;
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="logo">
        <h2>🛒 Smart Buyer</h2>
        <p>Fresh Agriculture Market</p>
    </div>

    <div class="menu">

        <a href="buyer_dashboard.php">
            <i class="fa fa-home"></i>
            Dashboard
        </a>

        <a href="buyer_orders.php" class="active">
            <i class="fa fa-cart-shopping"></i>
            My Orders
        </a>

        <a href="wishlist_page.php">
            <i class="fa fa-heart"></i>
            Wishlist
        </a>

        <a href="#">
            <i class="fa fa-bell"></i>
            Notifications
        </a>

        <a href="logout.php">
            <i class="fa fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</div>

<div class="main">

    <div class="header">

        <div>

            <h2>
                My Orders 📦
            </h2>

            <p style="color:#64748b;">
                Track all your fresh farm orders
            </p>

        </div>

        <div class="profile">

            <b>
                <?php echo htmlspecialchars($buyer['name']); ?>
            </b>

        </div>

    </div>

    <h3 class="section-title">
        Recent Orders
    </h3>

    <?php if(mysqli_num_rows($orders) > 0) { ?>

    <div class="orders-grid">

    <?php while($row = mysqli_fetch_assoc($orders)) { ?>

        <div class="order-card">

            <div class="order-image">

                <img src="uploads/products/<?php echo $row['image']; ?>">

            </div>

            <div class="order-content">

                <h3>
                    <?php echo htmlspecialchars($row['product_name']); ?>
                </h3>

                <div class="info">

                    <i class="fa fa-user"></i>

                    Farmer:
                    <?php echo htmlspecialchars($row['full_name']); ?>

                </div>

                <div class="info">

                    <i class="fa fa-calendar"></i>

                    Order Date:
                    <?php echo date("d M Y", strtotime($row['created_at'])); ?>

                </div>

                <div class="info">

                    <i class="fa fa-weight-hanging"></i>

                    Quantity:
                    <?php echo $row['quantity']; ?> KG

                </div>

                <div class="info">

                    <i class="fa fa-receipt"></i>

                    Order ID:
                    #<?php echo $row['order_id']; ?>

                </div>

                <div class="price">

                    ₹<?php echo number_format($row['total_price']); ?>

                </div>
                <div class="info">

    <i class="fa fa-money-bill-wave"></i>

    Payment:
    <?php echo $row['payment_method']; ?>

</div>

<div class="info">

    <i class="fa fa-wallet"></i>

    Payment Status:
    <?php echo $row['payment_status']; ?>

</div>

<?php

$status = $row['order_status'];

if($status == 'Pending')
{
    echo "<span class='status pending'>Pending Approval</span>";
}
elseif($status == 'Accepted')
{
    echo "<span class='status accepted'>Accepted by Farmer</span>";
}
elseif($status == 'Ready For Pickup')
{
    echo "<span class='status ready'>Ready For Pickup</span>";
}
elseif($status == 'Picked Up')
{
    echo "<span class='status picked'>Picked Up By Delivery Boy</span>";
}
elseif($status == 'Out For Delivery')
{
    echo "<span class='status out'>Out For Delivery</span>";
}
elseif($status == 'Delivered')
{
    echo "<span class='status delivered'>Delivered Successfully</span>";
}
elseif($status == 'Rejected')
{
    echo "<span class='status rejected'>Rejected</span>";
}

?>

<?php if($row['order_status'] == 'Pending') { ?>

<a
href="cancel_order.php?order_id=<?php echo $row['order_id']; ?>"
class="cancel-btn"
onclick="return confirm('Are you sure you want to cancel this order?')"
>
    <i class="fa fa-trash"></i>
    Cancel Order
</a>

<?php } ?>

            </div>

        </div>

    <?php } ?>

    </div>

    <?php } else { ?>

    <div class="empty-box">

        <i class="fa fa-box-open"></i>

        <h3>
            No Orders Found
        </h3>

        <p>
            Start buying fresh products from farmers.
        </p>

    </div>

    <?php } ?>

</div>

</body>
</html>