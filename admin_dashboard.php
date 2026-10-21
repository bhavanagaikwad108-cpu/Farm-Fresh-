<?php
session_start();
include("db.php");

/* ADMIN LOGIN CHECK */

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

/* TOTAL FARMERS */

$total_farmers = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM farmer
"));

/* TOTAL BUYERS */

$total_buyers = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM buyer
"));

/* TOTAL PRODUCTS */

$total_products = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM product
"));

/* TOTAL ORDERS */

$total_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
"));

/* READY ORDERS */

$ready_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Ready For Pickup'
"));

/* DELIVERED ORDERS */

$delivered_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Delivered'
"));

/* TOTAL SALES */

$sales_query = mysqli_query($conn,"
SELECT SUM(total_price) AS total_sales
FROM orders
WHERE order_status='Delivered'
");

$sales_data = mysqli_fetch_assoc($sales_query);

$total_sales = $sales_data['total_sales'];

if($total_sales == "")
{
    $total_sales = 0;
}

/* PENDING COD PAYMENTS */

$pending_payments = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE payment_method='Cash On Delivery'
AND payment_status='Pending'
"));

/* PAID ORDERS */

$paid_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE payment_status='Paid'
"));

/* RECENT PRODUCTS */

$recent_products = mysqli_query($conn,"
SELECT *
FROM product
ORDER BY product_id DESC
LIMIT 5
");

/* RECENT FARMERS */

$recent_farmers = mysqli_query($conn,"
SELECT *
FROM farmer
ORDER BY farmer_id DESC
LIMIT 5
");

/* RECENT BUYERS */

$recent_buyers = mysqli_query($conn,"
SELECT *
FROM buyer
ORDER BY buyer_id DESC
LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

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
    background:#f5f3ff;
}

/* SIDEBAR */

.sidebar{
    width:270px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#4c1d95,#7c3aed);
    padding:25px 20px;
    overflow-y:auto;
}

.logo{
    color:white;
    font-size:30px;
    font-weight:700;
    text-align:center;
    margin-bottom:40px;
}

.logo span{
    color:#ddd6fe;
}

.menu a{
    display:flex;
    align-items:center;
    gap:14px;
    color:white;
    text-decoration:none;
    padding:15px 18px;
    margin-bottom:15px;
    border-radius:16px;
    transition:0.3s;
    font-size:15px;
    font-weight:500;
}

.menu a:hover{
    background:rgba(255,255,255,0.12);
    transform:translateX(5px);
}

/* MAIN CONTENT */

.main-content{
    margin-left:270px;
    padding:30px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}

.topbar h1{
    color:#2e1065;
    font-size:34px;
    font-weight:700;
}

.admin-box{
    background:white;
    padding:12px 18px;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    display:flex;
    align-items:center;
    gap:10px;
}

.admin-box i{
    color:#7c3aed;
    font-size:20px;
}

/* DASHBOARD GRID */

.dashboard-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:25px;
}

.card-box{
    background:white;
    border-radius:24px;
    padding:28px;
    position:relative;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:0.3s;
}

.card-box:hover{
    transform:translateY(-6px);
}

.card-box::before{
    content:'';
    width:120px;
    height:120px;
    background:rgba(124,58,237,0.08);
    position:absolute;
    border-radius:50%;
    top:-30px;
    right:-30px;
}

.icon-box{
    width:70px;
    height:70px;
    background:linear-gradient(135deg,#7c3aed,#9333ea);
    color:white;
    border-radius:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    margin-bottom:18px;
}

.card-box h3{
    color:#64748b;
    font-size:16px;
    margin-bottom:10px;
}

.card-box h2{
    color:#1e1b4b;
    font-size:38px;
    font-weight:700;
}

.card-btn{
    display:inline-block;
    margin-top:20px;
    padding:12px 18px;
    background:linear-gradient(135deg,#7c3aed,#9333ea);
    color:white;
    text-decoration:none;
    border-radius:14px;
    font-size:14px;
    font-weight:600;
}

.card-btn:hover{
    opacity:0.9;
    color:white;
}

/* DELIVERY SECTION */

.delivery-banner{
    margin-top:40px;
    background:linear-gradient(135deg,#5b21b6,#7c3aed);
    border-radius:30px;
    padding:35px;
    color:white;
    position:relative;
    overflow:hidden;
}

.delivery-banner::before{
    content:'';
    width:220px;
    height:220px;
    background:rgba(255,255,255,0.08);
    position:absolute;
    right:-60px;
    top:-60px;
    border-radius:50%;
}

.delivery-banner h2{
    font-size:34px;
    margin-bottom:15px;
}

.delivery-banner p{
    color:#ede9fe;
    max-width:650px;
    margin-bottom:25px;
}

.delivery-btn{
    display:inline-block;
    background:white;
    color:#5b21b6;
    text-decoration:none;
    padding:14px 22px;
    border-radius:16px;
    font-weight:600;
}

.delivery-btn:hover{
    color:#5b21b6;
}

/* SECTION */

.section-box{
    margin-top:40px;
    background:white;
    padding:25px;
    border-radius:24px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
}

.section-box h2{
    margin-bottom:25px;
    color:#2e1065;
    font-size:26px;
    font-weight:700;
}

.table th{
    background:#f5f3ff;
    color:#4c1d95;
    padding:15px;
}

.table td{
    padding:15px;
    vertical-align:middle;
}

.product-img{
    width:70px;
    height:70px;
    border-radius:14px;
    object-fit:cover;
    border:2px solid #7c3aed;
}

/* RESPONSIVE */

@media(max-width:991px)
{
    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{
        margin-left:0;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        Farm<span>Admin</span>
    </div>

    <div class="menu">

        <a href="admin_dashboard.php">
            <i class="fa fa-chart-line"></i>
            Dashboard
        </a>

        <a href="manage_farmers.php">
            <i class="fa fa-users"></i>
            Farmers
        </a>

        <a href="manage_buyers.php">
            <i class="fa fa-user"></i>
            Buyers
        </a>

        <a href="manage_products.php">
            <i class="fa fa-box"></i>
            Products
        </a>

        <a href="manage_orders.php">
            <i class="fa fa-cart-shopping"></i>
            Orders
        </a>

        <a href="assign_delivery.php">
            <i class="fa fa-truck"></i>
            Assign Delivery
        </a>

        <a href="logout.php">
            <i class="fa fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</div>

<!-- MAIN CONTENT -->

<div class="main-content">

    <div class="topbar">

        <h1>Admin Dashboard</h1>

        <div class="admin-box">

            <i class="fa fa-user-shield"></i>

            Welcome Admin

        </div>

    </div>

    <!-- DASHBOARD CARDS -->

    <div class="dashboard-grid">

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-users"></i>
            </div>

            <h3>Total Farmers</h3>

            <h2><?php echo $total_farmers; ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-user"></i>
            </div>

            <h3>Total Buyers</h3>

            <h2><?php echo $total_buyers; ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-box"></i>
            </div>

            <h3>Total Products</h3>

            <h2><?php echo $total_products; ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-cart-shopping"></i>
            </div>

            <h3>Total Orders</h3>

            <h2><?php echo $total_orders; ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-truck-fast"></i>
            </div>

            <h3>Ready Orders</h3>

            <h2><?php echo $ready_orders; ?></h2>

            <a href="assign_delivery.php" class="card-btn">
                Assign Delivery
            </a>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-wallet"></i>
            </div>

            <h3>Total Sales</h3>

            <h2>₹<?php echo number_format($total_sales); ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-money-bill-wave"></i>
            </div>

            <h3>COD Pending</h3>

            <h2><?php echo $pending_payments; ?></h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-circle-check"></i>
            </div>

            <h3>Paid Orders</h3>

            <h2><?php echo $paid_orders; ?></h2>

        </div>

    </div>

    <!-- DELIVERY SECTION -->

    <div class="delivery-banner">

        <h2>
            🚚 Delivery Management System
        </h2>

        <p>
            Manage delivery operations, assign delivery boys and track all orders professionally.
        </p>

        <a href="assign_delivery.php" class="delivery-btn">
            Assign Delivery Now
        </a>

    </div>

    <!-- RECENT PRODUCTS -->

    <div class="section-box">

        <h2>
            Recently Added Products
        </h2>

        <table class="table table-bordered">

            <tr>

                <th>Image</th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>

            </tr>

            <?php while($row = mysqli_fetch_assoc($recent_products)) { ?>

            <tr>

                <td>

                    <?php if(!empty($row['image'])) { ?>

                    <img src="uploads/products/<?php echo $row['image']; ?>"
                    class="product-img">

                    <?php } ?>

                </td>

                <td>
                    <?php echo $row['product_name']; ?>
                </td>

                <td>
                    <?php echo $row['category']; ?>
                </td>

                <td>
                    ₹<?php echo $row['price']; ?>
                </td>

                <td>
                    <?php echo $row['quantity']; ?> KG
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>