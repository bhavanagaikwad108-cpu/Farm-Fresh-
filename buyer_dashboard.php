<?php
session_start();
include("db.php");

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

/* SEARCH */

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

/* FILTER */

$category = "";

if(isset($_GET['category']))
{
    $category = mysqli_real_escape_string($conn,$_GET['category']);
}

/* PRODUCT QUERY */

$product_query = "
SELECT
product.*,
farmer.full_name,
wishlist.product_id AS wishlisted
FROM product

JOIN farmer
ON product.farmer_id = farmer.farmer_id

LEFT JOIN wishlist
ON product.product_id = wishlist.product_id
AND wishlist.buyer_id = '$buyer_id'

WHERE product.quantity > 0
";
/* SEARCH */

if(!empty($search))
{
    $product_query .= "
    AND product.product_name LIKE '%$search%'
    ";
}

/* FILTER */

if(!empty($category))
{
    $product_query .= "
    AND product.category='$category'
    ";
}

$product_query .= "
ORDER BY product.product_id DESC
";

$products = mysqli_query($conn,$product_query);

/* TOTAL ORDERS */

$total_orders_query = mysqli_query($conn,"
SELECT COUNT(*) AS total_orders
FROM orders
WHERE buyer_id='$buyer_id'
");

$total_orders = mysqli_fetch_assoc($total_orders_query)['total_orders'];

/* TOTAL SPENT */

$total_spent_query = mysqli_query($conn,"
SELECT SUM(total_price) AS total_spent
FROM orders
WHERE buyer_id='$buyer_id'
");

$total_spent = mysqli_fetch_assoc($total_spent_query)['total_spent'];

if(!$total_spent)
{
    $total_spent = 0;
}

/* RECENT ORDERS */

$recent_orders = mysqli_query($conn,"
SELECT
orders.*,
product.product_name
FROM orders
JOIN product
ON orders.product_id = product.product_id
WHERE orders.buyer_id='$buyer_id'
ORDER BY orders.order_id DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Buyer Dashboard</title>

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

.menu a:hover{
    background:rgba(255,255,255,0.15);
    transform:translateX(5px);
}

.active-menu{
    background:rgba(255,255,255,0.15);
}

/* MAIN */

.main{
    margin-left:260px;
    padding:25px;
}

/* HEADER */

.header{
    background:white;
    padding:20px;
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
    padding:10px 16px;
    border-radius:50px;
}

/* CARDS */

.cards{
    margin-top:25px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
}

.card-box{
    background:white;
    padding:24px;
    border-radius:20px;
    position:relative;
    overflow:hidden;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
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
}

/* SEARCH */

.search-box{
    margin-top:30px;
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

.search-flex{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.search-flex input,
.search-flex select{
    flex:1;
    padding:13px;
    border-radius:12px;
    border:1px solid #cbd5e1;
}

.search-flex button{
    padding:13px 25px;
    border:none;
    border-radius:12px;
    background:#7e22ce;
    color:white;
    font-weight:600;
}

/* PRODUCTS */

.section-title{
    margin-top:35px;
    margin-bottom:18px;
    font-size:25px;
    font-weight:700;
}

.products{
    display:flex;
    flex-wrap:wrap;
    gap:22px;
}

.product-card{
    width:250px;
    background:white;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    transition:0.3s;
}

.product-card:hover{
    transform:translateY(-5px);
}

.product-image{
    width:100%;
    height:200px;
    position:relative;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.wishlist-btn{
    position:absolute;
    top:12px;
    right:12px;
    width:42px;
    height:42px;
    background:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    z-index:10;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    transition:0.3s;
}

.wishlist-btn i{
    font-size:18px;
    color:#64748b;
    transition:0.3s;
}

.wishlist-btn.active i{
    color:red;
    font-weight:900;
    transform:scale(1.1);
}

.product-content{
    padding:16px;
}

.product-content h4{
    font-size:18px;
    font-weight:600;
}

.product-content p{
    color:#64748b;
    margin-bottom:6px;
}

.price{
    color:#7e22ce;
    font-size:24px;
    font-weight:700;
}

.buy-form{
    margin-top:15px;
}

.buy-form input{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1px solid #cbd5e1;
    margin-bottom:10px;
}

.buy-btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#7e22ce,#9333ea);
    color:white;
    font-weight:600;
}

/* EMPTY */

.empty-box{
    width:100%;
    background:white;
    padding:50px;
    border-radius:22px;
    text-align:center;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

.empty-box i{
    font-size:70px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-box h3{
    color:#64748b;
    font-weight:600;
}

/* TABLE */

.orders-box{
    background:white;
    padding:20px;
    border-radius:22px;
    margin-top:25px;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

/* STATUS */

.status{
    padding:6px 14px;
    border-radius:30px;
    font-size:12px;
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

.delivered{
    background:#dcfce7;
    color:#166534;
}

.rejected{
    background:#fee2e2;
    color:#991b1b;
}

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

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        <h2>🛒 Smart Buyer</h2>
        <p>Fresh Agriculture Market</p>
    </div>

    <div class="menu">

        <a href="#" class="active-menu">
            <i class="fa fa-home"></i>
            Dashboard
        </a>

        <a href="buyer_orders.php">
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

<!-- MAIN -->

<div class="main">

    <!-- HEADER -->

    <div class="header">

        <div>

            <h2>
                Welcome,
                <?php echo htmlspecialchars($buyer['name']); ?> 👋
            </h2>

            <p style="color:#64748b;">
                Buy fresh products directly from farmers
            </p>

        </div>

        <div class="profile">

            <b>
                Buyer Account
            </b>

        </div>

    </div>

    <!-- CARDS -->

    <div class="cards">

        <div class="card-box">

            <i class="fa fa-cart-shopping"></i>

            <h2><?php echo $total_orders; ?></h2>

            <p>Total Orders</p>

        </div>

        <div class="card-box">

            <i class="fa fa-wallet"></i>

            <h2>₹<?php echo number_format($total_spent); ?></h2>

            <p>Total Spending</p>

        </div>

    </div>

    <!-- SEARCH -->

    <div class="search-box">

        <form method="GET">

            <div class="search-flex">

                <input
                type="text"
                name="search"
                placeholder="Search products..."
                value="<?php echo htmlspecialchars($search); ?>"
                >

                <select name="category">

                    <option value="">
                        All Categories
                    </option>

                    <option value="Vegetable" <?php if($category=="Vegetable") echo "selected"; ?>>
                        Vegetable
                    </option>

                    <option value="Fruit" <?php if($category=="Fruit") echo "selected"; ?>>
                        Fruit
                    </option>

                    <option value="Grain" <?php if($category=="Grain") echo "selected"; ?>>
                        Grain
                    </option>

                </select>

                <button type="submit">
                    Search
                </button>

            </div>

        </form>

    </div>

    <!-- PRODUCTS -->

    <h3 class="section-title">
        Fresh Products
    </h3>

    <div class="products">

    <?php if(mysqli_num_rows($products) > 0) { ?>

    <?php while($row = mysqli_fetch_assoc($products)) { ?>

    <?php

  $is_wishlisted = !empty($row['wishlisted']);

    ?>

    <div class="product-card">

        <div class="product-image">

            <span
            class="wishlist-btn <?php if($is_wishlisted){ echo 'active'; } ?>"
            data-product="<?php echo $row['product_id']; ?>"
            >

            <i class="<?php echo $is_wishlisted ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>

            </span>

            <img src="uploads/products/<?php echo $row['image']; ?>">

        </div>

        <div class="product-content">

            <h4>
                <?php echo htmlspecialchars($row['product_name']); ?>
            </h4>

            <p>
                Farmer:
                <?php echo htmlspecialchars($row['full_name']); ?>
            </p>

            <p>
                Available:
                <?php echo $row['quantity']; ?> KG
            </p>

            <div class="price">
                ₹<?php echo $row['price']; ?>/KG
            </div>

            <!-- BUY FORM -->

            <form
            action="place_order.php"
            method="POST"
            class="buy-form"
            >

                <input
                type="hidden"
                name="product_id"
                value="<?php echo $row['product_id']; ?>"
                >

                <input
                type="number"
                name="quantity"
                min="1"
                max="<?php echo $row['quantity']; ?>"
                placeholder="Enter Quantity"
                required
                >

                <button
                type="submit"
                class="buy-btn"
                >
                    Buy Now
                </button>

            </form>

        </div>

    </div>

    <?php } ?>

    <?php } else { ?>

    <div class="empty-box">

        <i class="fa fa-box-open"></i>

        <h3>
            Product Not Available
        </h3>

    </div>

    <?php } ?>

    </div>

    <!-- RECENT ORDERS -->

    <h3 class="section-title">
        Recent Orders
    </h3>

    <div class="orders-box">

        <table class="table">

            <thead>

                <tr>

                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

            <?php while($order = mysqli_fetch_assoc($recent_orders)) { ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($order['product_name']); ?>
                    </td>

                    <td>
                        <?php echo $order['quantity']; ?> KG
                    </td>

                    <td>
                        ₹<?php echo $order['total_price']; ?>
                    </td>

                    <td>

                        <?php
                        $status = $order['order_status'];

                        if($status == 'Pending')
                        {
                            echo "<span class='status pending'>Pending</span>";
                        }
                        elseif($status == 'Accepted')
                        {
                            echo "<span class='status accepted'>Accepted</span>";
                        }
                        elseif($status == 'Delivered')
                        {
                            echo "<span class='status delivered'>Delivered</span>";
                        }
                        else
                        {
                            echo "<span class='status rejected'>Rejected</span>";
                        }
                        ?>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<script>

document.querySelectorAll('.wishlist-btn').forEach(button => {

    button.addEventListener('click', function(){

        let productId = this.getAttribute('data-product');

        let currentButton = this;

        fetch('wishlist.php',{

            method:'POST',

            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },

            body:'product_id=' + productId

        })

        .then(response => response.text())

        .then(data => {

            let icon = currentButton.querySelector('i');

            if(data.trim() == "added")
            {
                currentButton.classList.add('active');

                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
            }
            else
            {
                currentButton.classList.remove('active');

                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
            }

        });

    });

});

</script>

</body>
</html>