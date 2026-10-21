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

/* FETCH WISHLIST PRODUCTS */

$query = mysqli_query($conn,"
SELECT
wishlist.*,
product.product_name,
product.price,
product.quantity,
product.image,
farmer.full_name
FROM wishlist
JOIN product
ON wishlist.product_id = product.product_id
JOIN farmer
ON product.farmer_id = farmer.farmer_id
WHERE wishlist.buyer_id='$buyer_id'
ORDER BY wishlist.wishlist_id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>My Wishlist</title>

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
    background:rgba(255,255,255,0.18);
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

/* PRODUCTS */

.section-title{
    margin-top:30px;
    margin-bottom:20px;
    font-size:28px;
    font-weight:700;
}

.products{
    display:flex;
    flex-wrap:wrap;
    gap:22px;
}

.product-card{
    width:260px;
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
    height:220px;
    position:relative;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.remove-btn{
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
    color:red;
    cursor:pointer;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

.product-content{
    padding:18px;
}

.product-content h4{
    font-size:20px;
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

.buy-btn{
    width:100%;
    margin-top:15px;
    padding:12px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#7e22ce,#9333ea);
    color:white;
    font-weight:600;
}

.empty{
    background:white;
    padding:40px;
    border-radius:20px;
    text-align:center;
    font-size:20px;
    color:#64748b;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
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

        <a href="buyer_dashboard.php">
            <i class="fa fa-home"></i>
            Dashboard
        </a>

        <a href="buyer_orders.php">
            <i class="fa fa-cart-shopping"></i>
            My Orders
        </a>

        <a href="wishlist_page.php" class="active-menu">
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

    <div class="header">

        <div>

            <h2>
                ❤️ My Wishlist
            </h2>

            <p style="color:#64748b;">
                Save your favourite farm products
            </p>

        </div>

        <div>

            <b>
                <?php echo htmlspecialchars($buyer['name']); ?>
            </b>

        </div>

    </div>

    <h3 class="section-title">
        Wishlist Products
    </h3>

    <?php if(mysqli_num_rows($query) > 0) { ?>

    <div class="products">

        <?php while($row = mysqli_fetch_assoc($query)) { ?>

        <div class="product-card">

            <div class="product-image">

                <div
                class="remove-btn"
                data-product="<?php echo $row['product_id']; ?>"
                >
                    <i class="fa-solid fa-heart"></i>
                </div>

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

                <form action="place_order.php" method="POST">

                    <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo $row['product_id']; ?>"
                    >

                    

                </form>

            </div>

        </div>

        <?php } ?>

    </div>

    <?php } else { ?>

    <div class="empty">
        No products in wishlist ❤️
    </div>

    <?php } ?>

</div>

<script>

document.querySelectorAll('.remove-btn').forEach(button => {

    button.addEventListener('click', function(){

        let productId = this.getAttribute('data-product');

        let currentCard = this.closest('.product-card');

        fetch('wishlist.php',{

            method:'POST',

            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },

            body:'product_id=' + productId

        })

        .then(response => response.text())

        .then(data => {

            currentCard.remove();

        });

    });

});

</script>

</body>
</html>