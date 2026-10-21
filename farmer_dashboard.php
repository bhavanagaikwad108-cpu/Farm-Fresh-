<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

/* FARMER DETAILS */
$q = "SELECT * FROM farmer WHERE farmer_id = ?";
$s = mysqli_prepare($conn, $q);
mysqli_stmt_bind_param($s, "i", $farmer_id);
mysqli_stmt_execute($s);
$farmer = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

/* TOTAL PRODUCTS */
$q1 = "SELECT COUNT(*) AS total_products FROM product WHERE farmer_id = ?";
$s1 = mysqli_prepare($conn, $q1);
mysqli_stmt_bind_param($s1, "i", $farmer_id);
mysqli_stmt_execute($s1);
$total_products = mysqli_fetch_assoc(mysqli_stmt_get_result($s1))['total_products'];

/* TOTAL QUANTITY */
$q2 = "SELECT SUM(quantity) AS total_quantity FROM product WHERE farmer_id = ?";
$s2 = mysqli_prepare($conn, $q2);
mysqli_stmt_bind_param($s2, "i", $farmer_id);
mysqli_stmt_execute($s2);
$total_quantity = mysqli_fetch_assoc(mysqli_stmt_get_result($s2))['total_quantity'];

if (!$total_quantity) {
    $total_quantity = 0;
}

/* TOTAL VALUE */
$q3 = "SELECT SUM(quantity * price) AS total_value FROM product WHERE farmer_id = ?";
$s3 = mysqli_prepare($conn, $q3);
mysqli_stmt_bind_param($s3, "i", $farmer_id);
mysqli_stmt_execute($s3);
$total_value = mysqli_fetch_assoc(mysqli_stmt_get_result($s3))['total_value'];

if (!$total_value) {
    $total_value = 0;
}

/* TOTAL ORDERS */
$total_orders_query = mysqli_query($conn,"
SELECT COUNT(*) AS total_orders
FROM orders
WHERE farmer_id='$farmer_id'
");

$total_orders = mysqli_fetch_assoc($total_orders_query)['total_orders'];

/* PENDING ORDERS */
$pending_orders_query = mysqli_query($conn,"
SELECT COUNT(*) AS pending_orders
FROM orders
WHERE farmer_id='$farmer_id'
AND order_status='Pending'
");

$pending_orders = mysqli_fetch_assoc($pending_orders_query)['pending_orders'];

/* PRODUCTS */
$products = mysqli_query($conn,"
SELECT * FROM product
WHERE farmer_id='$farmer_id'
ORDER BY product_id DESC
LIMIT 12
");

/* RECEIVED ORDERS */
$orders = mysqli_query($conn,"
SELECT orders.*, product.product_name
FROM orders
JOIN product ON orders.product_id = product.product_id
WHERE orders.farmer_id='$farmer_id'
ORDER BY orders.order_id DESC
LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Farmer Dashboard</title>

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
    overflow-x:hidden;
}

/* SIDEBAR */

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:linear-gradient(180deg,#0f172a,#1e293b);
    padding:25px 18px;
    color:white;
    z-index:1000;
}

.logo{
    text-align:center;
    margin-bottom:35px;
}

.logo h2{
    font-size:24px;
    font-weight:700;
    color:#4ade80;
}

.logo p{
    color:#cbd5e1;
    font-size:13px;
}

.menu{
    margin-top:25px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    text-decoration:none;
    color:#e2e8f0;
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:12px;
    transition:0.3s;
    font-size:15px;
}

.menu a:hover{
    background:#22c55e;
    transform:translateX(5px);
    color:white;
}

.menu i{
    font-size:18px;
}

/* MAIN */

.main{
    margin-left:260px;
    padding:25px;
}

/* HEADER */

.header{
    background:white;
    border-radius:22px;
    padding:20px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 20px rgba(0,0,0,0.06);
}

.welcome h1{
    font-size:28px;
    font-weight:700;
    color:#0f172a;
}

.welcome p{
    margin-top:5px;
    color:#64748b;
    font-size:14px;
}

.profile-box{
    display:flex;
    align-items:center;
    gap:14px;
    background:#f8fafc;
    padding:10px 16px;
    border-radius:50px;
}

.profile-box img{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #22c55e;
}

.profile-box h5{
    margin:0;
    font-size:16px;
    font-weight:600;
}

.profile-box span{
    font-size:12px;
    color:#64748b;
}

/* BANNER */

.banner{
    margin-top:25px;
    background:linear-gradient(135deg,#16a34a,#22c55e);
    border-radius:25px;
    padding:35px;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    overflow:hidden;
    position:relative;
}

.banner::before{
    content:'';
    position:absolute;
    width:250px;
    height:250px;
    background:purple(255,255,255,0.1);
    border-radius:50%;
    right:-70px;
    top:-70px;
}

.banner h2{
    font-size:34px;
    font-weight:700;
}

.banner p{
    margin-top:10px;
    font-size:15px;
    color:#dcfce7;
}

.banner i{
    font-size:110px;
    opacity:0.2;
}

/* STATS */

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-top:25px;
}

.card-box{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    transition:0.3s;
    position:relative;
    overflow:hidden;
}

.card-box:hover{
    transform:translateY(-6px);
}

.card-box i{
    position:absolute;
    right:20px;
    top:20px;
    font-size:55px;
    opacity:0.1;
}

.card-box h2{
    font-size:34px;
    font-weight:700;
    margin-bottom:5px;
}

.card-box p{
    color:#64748b;
    margin:0;
}

/* PRODUCT SECTION */

.section-header{
    margin-top:35px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.section-header h3{
    font-size:24px;
    font-weight:700;
    color:#0f172a;
}

/* PRODUCTS */

.products{
    display:flex;
    flex-wrap:wrap;
    gap:25px;
}

.product-card{
    width:150px;
    text-align:center;
    background:white;
    padding:15px;
    border-radius:22px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    transition:0.3s;
}

.product-card:hover{
    transform:translateY(-6px);
}

.product-image{
    width:90px;
    height:90px;
    margin:auto;
    border-radius:50%;
    overflow:hidden;
    border:3px solid #22c55e;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.product-content h4{
    font-size:15px;
    font-weight:600;
    margin-top:12px;
    color:#0f172a;
}

/* ORDER TABLE */

.orders-box{
    background:white;
    padding:20px;
    border-radius:22px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    overflow-x:auto;
}

.status-pending{
    background:#fef3c7;
    color:#92400e;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-delivered{
    background:#dcfce7;
    color:#166534;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

/* RESPONSIVE */

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
        gap:20px;
        align-items:flex-start;
    }

    .banner{
        flex-direction:column;
        text-align:center;
        gap:20px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        <h2>🌾 Smart Farming</h2>
        <p>Modern Farmer Dashboard</p>
    </div>

    <div class="menu">
        

        <a href="#">
            <i class="fa fa-home"></i>
            Dashboard
        </a>

        <a href="add_product.php">
            <i class="fa fa-plus-circle"></i>
            Add Product
        </a>

        <a href="my_products.php">
            <i class="fa fa-box"></i>
            My Products
        </a>
        <a href="my_order.php">
    <i class="fa fa-cart-shopping"></i>
    My Orders
</a>

   <a href="analytics.php">
    <i class="fa fa-chart-line"></i>
    Analytics
</a>

<a href="earnings.php">
    <i class="fa fa-wallet"></i>
    Earnings
</a>

        <a href="logout.php">
            <i class="fa fa-sign-out-alt"></i>
            Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <!-- HEADER -->

    <div class="header">

        <div class="welcome">
            <h1>Hello, <?php echo htmlspecialchars($farmer['full_name']); ?> 👋</h1>
            <p>Manage your farming products professionally and grow your business.</p>
        </div>

        <div class="profile-box">

            <?php if(!empty($farmer['profile_image'])) { ?>

                <img src="uploads/farmers/<?php echo $farmer['profile_image']; ?>">

            <?php } else { ?>

                <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">

            <?php } ?>

            <div>
                <h5><?php echo htmlspecialchars($farmer['full_name']); ?></h5>
                <span>Farmer Account</span>
            </div>

        </div>

    </div>

    <!-- BANNER -->

    <div class="banner">

        <div>
            <h2>Smart Agriculture System</h2>
            <p>Track products, orders and earnings digitally.</p>
        </div>

        <i class="fa-solid fa-seedling"></i>

    </div>

    <!-- STATS -->

    <div class="stats">

        <div class="card-box">
            <i class="fa fa-box"></i>
            <h2><?php echo $total_products; ?></h2>
            <p>Total Products</p>
        </div>

        <div class="card-box">
            <i class="fa fa-weight-hanging"></i>
            <h2><?php echo $total_quantity; ?> KG</h2>
            <p>Total Quantity</p>
        </div>

        <div class="card-box">
            <i class="fa fa-indian-rupee-sign"></i>
            <h2>₹<?php echo number_format($total_value); ?></h2>
            <p>Total Product Value</p>
        </div>

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
            <i class="fa fa-leaf"></i>
            <h2><?php echo htmlspecialchars($farmer['crop_type']); ?></h2>
            <p>Main Crop</p>
        </div>

    </div>

    <!-- PRODUCTS -->

    <div class="section-header">
        <h3>Your Products</h3>
    </div>

    <div class="products">

        <?php while($row = mysqli_fetch_assoc($products)) { ?>

        <div class="product-card">

            <div class="product-image">
                <img src="uploads/products/<?php echo $row['image']; ?>">
            </div>

            <div class="product-content">

                <h4>
                    <?php echo htmlspecialchars($row['product_name']); ?>
                </h4>

                <p style="margin-top:5px;color:#64748b;font-size:13px;">
                    <?php echo $row['quantity']; ?> KG
                </p>

                <?php if($row['quantity'] <= 0){ ?>

                    <p style="color:red;font-weight:600;font-size:13px;">
                        Out Of Stock
                    </p>

                <?php } ?>

                <?php if($row['quantity'] < 10 && $row['quantity'] > 0){ ?>

                    <p style="color:orange;font-weight:600;font-size:13px;">
                        Low Stock
                    </p>

                <?php } ?>

            </div>

        </div>

        <?php } ?>

    </div>

    <!-- RECEIVED ORDERS -->

    <div class="section-header">
      
                 

    </div>

</div>

</body>
</html>