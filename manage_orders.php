
<?php
session_start();
include("db.php");

/* ADMIN LOGIN CHECK */

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

/* DELETE ORDER */

if(isset($_GET['delete']))
{
    $delete_id = intval($_GET['delete']);

    mysqli_query($conn,"DELETE FROM orders WHERE order_id='$delete_id'");

    header("Location: manage_orders.php");
    exit();
}

/* TOTAL ORDERS */

$total_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
"));

/* PENDING ORDERS */

$pending_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Pending'
"));

/* READY FOR PICKUP */

$pickup_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Ready For Pickup'
"));

/* OUT FOR DELIVERY */

$out_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Out For Delivery'
"));

/* DELIVERED */

$delivered_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
WHERE order_status='Delivered'
"));

/* TOTAL SALES */

$sales = mysqli_query($conn,"
SELECT SUM(total_price) AS total_sales
FROM orders
WHERE order_status='Delivered'
");

$sales_data = mysqli_fetch_assoc($sales);
$total_sales = $sales_data['total_sales'];

if($total_sales == "")
{
    $total_sales = 0;
}

/* FETCH ORDERS */

$orders = mysqli_query($conn,"
SELECT
orders.*,
buyer.name AS buyer_name,
farmer.full_name AS farmer_name,
product.product_name,
product.image

FROM orders

LEFT JOIN buyer
ON orders.buyer_id = buyer.buyer_id

LEFT JOIN farmer
ON orders.farmer_id = farmer.farmer_id

LEFT JOIN product
ON orders.product_id = product.product_id

ORDER BY orders.order_id DESC
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Orders</title>

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
    background:#f5f3ff;
    font-family:'Poppins',sans-serif;
}

.sidebar{
    width:270px;
    height:100vh;
    background:#111827;
    position:fixed;
    top:0;
    left:0;
    padding:25px 20px;
}

.logo{
    color:white;
    text-align:center;
    font-size:30px;
    font-weight:700;
    margin-bottom:40px;
}

.logo span{
    color:#a78bfa;
}

.menu a{
    display:flex;
    align-items:center;
    gap:14px;
    color:white;
    text-decoration:none;
    padding:14px 18px;
    margin-bottom:15px;
    border-radius:16px;
    transition:0.3s;
}

.menu a:hover,
.active{
    background:#7c3aed;
}

.main-content{
    margin-left:270px;
    padding:30px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}

.topbar h1{
    font-size:34px;
    color:#2e1065;
    font-weight:700;
}

.admin-box{
    background:white;
    padding:12px 18px;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.card-box{
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:0.3s;
}

.card-box:hover{
    transform:translateY(-5px);
}

.icon-box{
    width:65px;
    height:65px;
    border-radius:18px;
    background:#ede9fe;
    color:#7c3aed;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    margin-bottom:15px;
}

.card-box h3{
    font-size:15px;
    color:#64748b;
    margin-bottom:8px;
}

.card-box h2{
    font-size:34px;
    color:#111827;
    font-weight:700;
}

.orders-section{
    background:white;
    border-radius:25px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
}

.section-title{
    font-size:28px;
    color:#2e1065;
    margin-bottom:25px;
    font-weight:700;
}

.table th{
    background:#ede9fe;
    color:#4c1d95;
    padding:16px;
    font-size:14px;
}

.table td{
    padding:16px;
    vertical-align:middle;
    font-size:14px;
}

.product-img{
    width:70px;
    height:70px;
    border-radius:14px;
    object-fit:cover;
    border:2px solid #7c3aed;
}

.status{
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}

.pending{
    background:#fef3c7;
    color:#92400e;
}

.accepted{
    background:#dbeafe;
    color:#1d4ed8;
}

.pickup{
    background:#ede9fe;
    color:#6d28d9;
}

.delivery{
    background:#cffafe;
    color:#155e75;
}

.delivered{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

.delete-btn{
    background:#dc2626;
    color:white;
    text-decoration:none;
    padding:10px 14px;
    border-radius:10px;
    font-size:13px;
    font-weight:600;
}

.delete-btn:hover{
    background:#b91c1c;
    color:white;
}

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

        <a href="manage_orders.php" class="active">
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

<div class="main-content">

    <div class="topbar">

        <h1>Manage Orders</h1>

        <div class="admin-box">
            <i class="fa fa-user-shield"></i>
            Welcome Admin
        </div>

    </div>

    <div class="stats-grid">

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-cart-shopping"></i>
            </div>
            <h3>Total Orders</h3>
            <h2><?php echo $total_orders; ?></h2>
        </div>

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-clock"></i>
            </div>
            <h3>Pending Orders</h3>
            <h2><?php echo $pending_orders; ?></h2>
        </div>

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-box"></i>
            </div>
            <h3>Ready For Pickup</h3>
            <h2><?php echo $pickup_orders; ?></h2>
        </div>

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-truck"></i>
            </div>
            <h3>Out For Delivery</h3>
            <h2><?php echo $out_orders; ?></h2>
        </div>

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-circle-check"></i>
            </div>
            <h3>Delivered Orders</h3>
            <h2><?php echo $delivered_orders; ?></h2>
        </div>

        <div class="card-box">
            <div class="icon-box">
                <i class="fa fa-wallet"></i>
            </div>
            <h3>Total Sales</h3>
            <h2>₹<?php echo number_format($total_sales); ?></h2>
        </div>

    </div>

    <div class="orders-section">

        <h2 class="section-title">
            All Orders
        </h2>

        <div class="table-responsive">

        <table class="table align-middle table-bordered">

            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Farmer</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>COD Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>

            <?php
            if(mysqli_num_rows($orders) > 0)
            {
                while($row = mysqli_fetch_assoc($orders))
                {
            ?>

            <tr>

                <td>
                    <?php if(!empty($row['image'])) { ?>

                    <img
                    src="uploads/products/<?php echo $row['image']; ?>"
                    class="product-img"
                    >

                    <?php } ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['product_name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['buyer_name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['farmer_name']); ?>
                </td>

                <td>
                    <?php echo $row['quantity']; ?> KG
                </td>

                <td>
                    ₹<?php echo number_format($row['total_price']); ?>
                </td>

                <td>
                    <?php
                    if(isset($row['payment_method']))
                    {
                        echo $row['payment_method'];
                    }
                    else
                    {
                        echo "Cash On Delivery";
                    }
                    ?>
                </td>

                <td>

                    <?php

                    if($row['order_status'] == 'Pending')
                    {
                        echo "<span class='status pending'>Pending</span>";
                    }
                    elseif($row['order_status'] == 'Accepted')
                    {
                        echo "<span class='status accepted'>Accepted</span>";
                    }
                    elseif($row['order_status'] == 'Ready For Pickup')
                    {
                        echo "<span class='status pickup'>Ready For Pickup</span>";
                    }
                    elseif($row['order_status'] == 'Picked Up')
                    {
                        echo "<span class='status pickup'>Picked Up</span>";
                    }
                    elseif($row['order_status'] == 'Out For Delivery')
                    {
                        echo "<span class='status delivery'>Out For Delivery</span>";
                    }
                    elseif($row['order_status'] == 'Delivered')
                    {
                        echo "<span class='status delivered'>Delivered</span>";
                    }
                    else
                    {
                        echo "<span class='status rejected'>Rejected</span>";
                    }

                    ?>

                </td>

                <td>
                    <?php
                    if(isset($row['created_at']))
                    {
                        echo date("d M Y", strtotime($row['created_at']));
                    }
                    else
                    {
                        echo date("d M Y");
                    }
                    ?>
                </td>

                <td>

                    <a
                    href="manage_orders.php?delete=<?php echo $row['order_id']; ?>"
                    class="delete-btn"
                    onclick="return confirm('Delete this order?')"
                    >
                        <i class="fa fa-trash"></i>
                        Delete
                    </a>

                </td>

            </tr>

            <?php
                }
            }
            else
            {
                echo "
                <tr>
                    <td colspan='10' style='text-align:center;'>
                        No Orders Found
                    </td>
                </tr>
                ";
            }
            ?>

        </table>

        </div>

    </div>

</div>

</body>
</html>
