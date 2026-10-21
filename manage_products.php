<?php
session_start();
include("db.php");

/* ADMIN LOGIN CHECK */

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

/* DELETE PRODUCT */

if(isset($_GET['delete']))
{
    $delete_id = intval($_GET['delete']);

    /* DELETE IMAGE */

    $img_query = mysqli_query($conn,"
    SELECT image
    FROM product
    WHERE product_id='$delete_id'
    ");

    $img_data = mysqli_fetch_assoc($img_query);

    if(!empty($img_data['image']))
    {
        $img_path = "uploads/products/".$img_data['image'];

        if(file_exists($img_path))
        {
            unlink($img_path);
        }
    }

    mysqli_query($conn,"
    DELETE FROM product
    WHERE product_id='$delete_id'
    ");

    header("Location: manage_products.php");
    exit();
}

/* TOTAL PRODUCTS */

$total_products = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM product
"));

/* TOTAL STOCK */

$stock_query = mysqli_query($conn,"
SELECT SUM(quantity) AS total_stock
FROM product
");

$stock_data = mysqli_fetch_assoc($stock_query);

$total_stock = $stock_data['total_stock'];

if($total_stock == "")
{
    $total_stock = 0;
}

/* TOTAL CATEGORIES */

$total_categories = mysqli_num_rows(mysqli_query($conn,"
SELECT DISTINCT category
FROM product
"));

/* LOW STOCK */

$low_stock = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM product
WHERE quantity <= 5
"));

/* FETCH PRODUCTS */

$products = mysqli_query($conn,"
SELECT
product.*,
farmer.full_name

FROM product

LEFT JOIN farmer
ON product.farmer_id = farmer.farmer_id

ORDER BY product.product_id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Products</title>

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

/* SIDEBAR */

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

/* MAIN */

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

/* STATS */

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

/* TABLE SECTION */

.products-section{
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
    width:80px;
    height:80px;
    border-radius:16px;
    object-fit:cover;
    border:2px solid #7c3aed;
}

/* STOCK */

.in-stock{
    background:#dcfce7;
    color:#166534;
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

.low-stock{
    background:#fee2e2;
    color:#991b1b;
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

/* BUTTONS */

.action-btn{
    padding:10px 14px;
    border-radius:10px;
    color:white;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
    display:inline-block;
}

.edit-btn{
    background:#2563eb;
}

.delete-btn{
    background:#dc2626;
}

.action-btn:hover{
    opacity:0.9;
    color:white;
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

        <a href="manage_products.php" class="active">
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

    <!-- TOPBAR -->

    <div class="topbar">

        <h1>Manage Products</h1>

        <div class="admin-box">
            <i class="fa fa-user-shield"></i>
            Welcome Admin
        </div>

    </div>

    <!-- STATS -->

    <div class="stats-grid">

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-box"></i>
            </div>

            <h3>Total Products</h3>

            <h2>
                <?php echo $total_products; ?>
            </h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-layer-group"></i>
            </div>

            <h3>Total Categories</h3>

            <h2>
                <?php echo $total_categories; ?>
            </h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-warehouse"></i>
            </div>

            <h3>Total Stock</h3>

            <h2>
                <?php echo $total_stock; ?> KG
            </h2>

        </div>

        <div class="card-box">

            <div class="icon-box">
                <i class="fa fa-triangle-exclamation"></i>
            </div>

            <h3>Low Stock</h3>

            <h2>
                <?php echo $low_stock; ?>
            </h2>

        </div>

    </div>

    <!-- PRODUCTS -->

    <div class="products-section">

        <h2 class="section-title">
            All Products
        </h2>

        <div class="table-responsive">

        <table class="table align-middle table-bordered">

            <tr>

                <th>Image</th>
                <th>Product</th>
                <th>Farmer</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Action</th>

            </tr>

            <?php

            if(mysqli_num_rows($products) > 0)
            {
                while($row = mysqli_fetch_assoc($products))
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
                    <?php echo htmlspecialchars($row['full_name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['category']); ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['price']); ?>
                </td>

                <td>
                    <?php echo $row['quantity']; ?> KG
                </td>

                <td>

                    <?php

                    if($row['quantity'] <= 5)
                    {
                        echo "<span class='low-stock'>Low Stock</span>";
                    }
                    else
                    {
                        echo "<span class='in-stock'>In Stock</span>";
                    }

                    ?>

                </td>

                <td>

                    <a
                    href="edit_product.php?id=<?php echo $row['product_id']; ?>"
                    class="action-btn edit-btn"
                    >
                        <i class="fa fa-pen"></i>
                    </a>

                    <a
                    href="manage_products.php?delete=<?php echo $row['product_id']; ?>"
                    class="action-btn delete-btn"
                    onclick="return confirm('Delete this product?')"
                    >
                        <i class="fa fa-trash"></i>
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
                    <td colspan='8' style='text-align:center;'>
                        No Products Found
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