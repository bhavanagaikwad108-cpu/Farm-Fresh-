<?php
include 'db.php';

/* FETCH ONLY 4 LATEST FARMER PRODUCTS */
$product_query = mysqli_query($conn,"
SELECT * FROM product
ORDER BY product_id DESC
LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmFresh - Smart Farming Marketplace</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        :root {
            --primary-color: #0b6623;
            --primary-gradient: linear-gradient(135deg,#0b6623,#148a36);
            --accent-color: #70e000;
            --bg-light: #f7f9f6;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border-color: #e2e8f0;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        html{
            scroll-behavior:smooth;
        }

        body{
            background:var(--bg-light);
            color:var(--text-main);
            overflow-x:hidden;
        }

        /* NAVBAR */

        .navbar{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            padding:15px 6%;
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(10px);
            z-index:1000;
            border-bottom:1px solid var(--border-color);
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }

        .logo-section{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .logo{
            width:45px;
            height:45px;
        }

        .logo-section h2{
            color:var(--primary-color);
            font-size:28px;
            font-weight:700;
        }

        .menu-buttons{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .menu-buttons button,
        .dropbtn{
            padding:10px 18px;
            border:none;
            border-radius:8px;
            background:#fff;
            border:1px solid var(--border-color);
            cursor:pointer;
            font-size:14px;
            font-weight:500;
            transition:0.3s;
        }

        .menu-buttons button:hover,
        .dropbtn:hover{
            background:var(--primary-color);
            color:#fff;
        }

        /* DROPDOWN */

        .dropdown{
            position:relative;
        }

        .dropdown-content{
            position:absolute;
            top:50px;
            right:0;
            background:#fff;
            min-width:220px;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 10px 25px rgba(0,0,0,0.1);
            opacity:0;
            visibility:hidden;
            transition:0.3s;
            z-index:999;
        }

        .dropdown:hover .dropdown-content{
            opacity:1;
            visibility:visible;
            top:55px;
        }

        .dropdown-content a{
            display:block;
            padding:14px 18px;
            text-decoration:none;
            color:#333;
            border-bottom:1px solid #eee;
            transition:0.3s;
            font-size:14px;
        }

        .dropdown-content a:hover{
            background:#f4fff4;
            color:var(--primary-color);
            padding-left:24px;
        }

        /* HERO SECTION */

        .hero{
            padding-top:100px;
            padding-bottom:50px;
            display:flex;
            justify-content:center;
        }

        .hero-container{
            width:90%;
            max-width:1250px;
            height:500px;
            position:relative;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 15px 40px rgba(0,0,0,0.08);
        }

        .slides{
            width:100%;
            height:100%;
            position:relative;
        }

        .slides img{
            position:absolute;
            width:100%;
            height:100%;
            object-fit:cover;
            opacity:0;
            animation:fade 20s infinite;
            filter:brightness(0.55);
        }

        .slides img:nth-child(1){ animation-delay:0s; }
        .slides img:nth-child(2){ animation-delay:5s; }
        .slides img:nth-child(3){ animation-delay:10s; }
        .slides img:nth-child(4){ animation-delay:15s; }

        @keyframes fade{
            0%,100%{ opacity:0; }
            5%,25%{ opacity:1; }
            30%{ opacity:0; }
        }

        .hero-content{
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            text-align:center;
            z-index:5;
            color:#fff;
            width:80%;
        }

        .hero-content h1{
            font-size:52px;
            margin-bottom:18px;
            font-weight:700;
        }

        .hero-content p{
            font-size:18px;
            line-height:1.7;
        }

        /* SECTION */

        .live-products,
        .trust-section,
        .ads-section{
            padding:70px 6%;
        }

        .section-title{
            text-align:center;
            font-size:36px;
            margin-bottom:10px;
            color:#222;
            font-weight:700;
        }

        .section-subtitle{
            text-align:center;
            color:#777;
            margin-bottom:45px;
            font-size:15px;
        }

        /* PRODUCTS GRID */

        .products-grid{
            width:100%;
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:25px;
        }

        .product-box{
            background:#fff;
            border-radius:18px;
            overflow:hidden;
            border:1px solid #e5e5e5;
            transition:0.3s;
            box-shadow:0 5px 15px rgba(0,0,0,0.06);
        }

        .product-box:hover{
            transform:translateY(-8px);
            box-shadow:0 15px 30px rgba(0,0,0,0.1);
        }

        .product-box img{
            width:100%;
            height:240px;
            object-fit:cover;
        }

        .product-details{
            padding:20px;
        }

        .product-details h2{
            font-size:22px;
            margin-bottom:10px;
            color:#222;
        }

        .product-price{
            font-size:28px;
            font-weight:700;
            color:var(--primary-color);
            margin-bottom:18px;
        }

        .product-details button{
            width:100%;
            padding:13px;
            border:none;
            border-radius:10px;
            background:var(--primary-gradient);
            color:#fff;
            font-size:15px;
            font-weight:600;
            cursor:pointer;
            transition:0.3s;
        }

        .product-details button:hover{
            transform:scale(1.02);
        }

        /* TRUST SECTION */

        .trust-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
            gap:25px;
        }

        .trust-box{
            background:#fff;
            border-radius:15px;
            padding:30px;
            text-align:center;
            border:1px solid #eee;
            transition:0.3s;
        }

        .trust-box:hover{
            transform:translateY(-5px);
        }

        .trust-icon{
            width:70px;
            height:70px;
            background:#efffed;
            color:var(--primary-color);
            display:flex;
            justify-content:center;
            align-items:center;
            border-radius:50%;
            margin:auto;
            font-size:30px;
            margin-bottom:18px;
        }

        .trust-box h2{
            margin-bottom:12px;
            font-size:20px;
        }

        .trust-box p{
            color:#777;
            line-height:1.7;
            font-size:14px;
        }

        /* ADS */

        .ads-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
            gap:25px;
        }

        .ad-card{
            background:#fff;
            border-radius:15px;
            overflow:hidden;
            border:1px solid #eee;
            position:relative;
            transition:0.3s;
        }

        .ad-card:hover{
            transform:translateY(-5px);
        }

        .ad-card img{
            width:100%;
            height:220px;
            object-fit:cover;
        }

        .ad-content{
            padding:20px;
        }

        .ad-content h2{
            margin-bottom:10px;
            font-size:20px;
        }

        .ad-content p{
            color:#777;
            line-height:1.6;
            font-size:14px;
            margin-bottom:15px;
        }

        .ad-price{
            font-size:24px;
            font-weight:700;
            color:var(--primary-color);
            margin-bottom:15px;
        }

        .ad-content button{
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            background:var(--primary-gradient);
            color:#fff;
            font-size:15px;
            cursor:pointer;
        }

        .ad-badge{
            position:absolute;
            top:12px;
            left:12px;
            background:#ff9800;
            color:#fff;
            padding:5px 10px;
            border-radius:6px;
            font-size:12px;
            font-weight:600;
        }

        /* FOOTER */

        footer{
            background:#1a202c;
            padding:60px 6% 30px;
            text-align:center;
            color:#fff;
        }

        footer h2{
            margin-bottom:10px;
            font-size:28px;
        }

        footer p{
            color:#ccc;
            margin-top:8px;
        }

        .socials{
            margin-top:20px;
            display:flex;
            justify-content:center;
            gap:12px;
        }

        .socials a{
            width:40px;
            height:40px;
            border-radius:8px;
            background:rgba(255,255,255,0.1);
            color:#fff;
            display:flex;
            justify-content:center;
            align-items:center;
            text-decoration:none;
            transition:0.3s;
        }

        .socials a:hover{
            background:var(--primary-color);
        }

        /* RESPONSIVE */

        @media(max-width:1100px){

            .products-grid{
                grid-template-columns:repeat(2,1fr);
            }

            .hero-content h1{
                font-size:42px;
            }

        }

        @media(max-width:700px){

            .products-grid{
                grid-template-columns:1fr;
            }

            .hero-content h1{
                font-size:30px;
            }

            .hero-container{
                height:400px;
            }

            .menu-buttons{
                gap:8px;
            }

            .menu-buttons button,
            .dropbtn{
                padding:8px 12px;
                font-size:12px;
            }

        }

    </style>

</head>

<body>

<!-- NAVBAR -->

<header class="navbar">

    <div class="logo-section">
        <img src="https://cdn-icons-png.flaticon.com/512/2909/2909767.png" class="logo">
        <h2>FarmFresh</h2>
    </div>

    <div class="menu-buttons">

        <div class="dropdown">

            <button class="dropbtn">
                Register ▼
            </button>

            <div class="dropdown-content">
                <a href="farmer_register.php">Farmer Register</a>
                <a href="buyer_register.php">Buyer Register</a>
            </div>

        </div>

        <div class="dropdown">

            <button class="dropbtn">
                Login ▼
            </button>

            <div class="dropdown-content">
                <a href="farmer_login.php">Farmer Login</a>
                <a href="buyer_login.php">Buyer Login</a>
                <a href="admin_login.php">Admin Login</a>
                <a href="delivery_login.php">Delivery Boy Login</a>
            </div>

        </div>

        <button onclick="window.location.href='#footer'">
            Contact
        </button>

    </div>

</header>

<!-- HERO -->

<section class="hero">

    <div class="hero-container">

        <div class="slides">

            <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854">
            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399">
            <img src="https://images.unsplash.com/photo-1471193945509-9ad0617afabf">
            <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6">

        </div>

        <div class="hero-content">

            <h1>
                Fresh Products Directly From Farmers
            </h1>

            <p>
                Organic vegetables, grains and fruits delivered fast to your home
            </p>

        </div>

    </div>

</section>

<!-- FARMER PRODUCTS -->

<section class="live-products">

   <h1 class="section-title">
    Fresh Farm Products
</h1>

<p class="section-subtitle">
    Products uploaded directly by farmers
</p>

<div class="products-grid">

    <?php
    while($row = mysqli_fetch_assoc($product_query))
    {
    ?>

    <div class="product-box">

        <img src="uploads/products/<?php echo $row['image']; ?>"
             alt="<?php echo $row['product_name']; ?>"
             class="product-image">

        <div class="product-details">

            <h2>
                <?php echo $row['product_name']; ?>
            </h2>

            <div class="product-price">
                ₹ <?php echo number_format($row['price']); ?>
            </div>

            <button onclick="window.location.href='buyer_register.php'">
                Buy Now
            </button>

        </div>

    </div>

    <?php
    }
    ?>

</div>

    </div>

</section>

<!-- TRUST SECTION -->

<section class="trust-section">

    <div class="trust-container">

        <div class="trust-box">
            <div class="trust-icon">✓</div>
            <h2>100% Branded Products</h2>
            <p>
                Trusted agricultural brands with certified premium quality provisions.
            </p>
        </div>

        <div class="trust-box">
            <div class="trust-icon">🌱</div>
            <h2>100% Original Products</h2>
            <p>
                Genuine farm fresh products straight from verified agricultural producers.
            </p>
        </div>

        <div class="trust-box">
            <div class="trust-icon">🚚</div>
            <h2>Free Delivery</h2>
            <p>
                Fast and reliable free home delivery on selected farming items.
            </p>
        </div>

        <div class="trust-box">
            <div class="trust-icon">🔒</div>
            <h2>Secure Payments</h2>
            <p>
                Safe and fully integrated payment processes protecting your transactions.
            </p>
        </div>

    </div>

</section>

<!-- ADS SECTION -->

<section class="ads-section">

    <h1 class="section-title">
        Farming Essentials & Machinery
    </h1>

    <p class="section-subtitle">
        Growth promoters, fungicides and smart agricultural gear
    </p>

    <div class="ads-container">

        <div class="ad-card">

            <span class="ad-badge">
                Sponsored
            </span>

            <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449">

            <div class="ad-content">

                <h2>Organic Growth Promoter</h2>

                <p>
                    Improve crop growth cycle and increase output productivity organically.
                </p>

                <div class="ad-price">
                    ₹799
                </div>

                <a href="https://www.amazon.in/s?k=growth+promoter+for+plants" target="_blank">
                    <button>Buy Now</button>
                </a>

            </div>

        </div>

        <div class="ad-card">

            <span class="ad-badge">
                Sponsored
            </span>

            <img src="https://images.unsplash.com/photo-1589923188900-85dae523342b">

            <div class="ad-content">

                <h2>Premium Fungicide</h2>

                <p>
                    Protect crops and plants effectively from harmful fungus patterns.
                </p>

                <div class="ad-price">
                    ₹499
                </div>

                <a href="https://www.flipkart.com/search?q=fungicide+for+plants" target="_blank">
                    <button>Buy Now</button>
                </a>

            </div>

        </div>

        <div class="ad-card">

            <span class="ad-badge">
                Sponsored
            </span>

            <img src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854">

            <div class="ad-content">

                <h2>Smart Farming Machine</h2>

                <p>
                    Modern advanced equipment engineered for efficient cultivation metrics.
                </p>

                <div class="ad-price">
                    ₹25,000
                </div>

                <a href="https://www.indiamart.com/proddetail/agriculture-machinery.html" target="_blank">
                    <button>View Product</button>
                </a>

            </div>

        </div>

        <div class="ad-card">

            <span class="ad-badge">
                Sponsored
            </span>

            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399">

            <div class="ad-content">

                <h2>Advanced Fertilizer</h2>

                <p>
                    High grade nutrient composition formulas for sustainable farm harvests.
                </p>

                <div class="ad-price">
                    ₹999
                </div>

                <a href="https://www.amazon.in/s?k=organic+fertilizer" target="_blank">
                    <button>Buy Now</button>
                </a>

            </div>

        </div>

    </div>

</section>

<!-- FOOTER -->

<footer id="footer">

    <h2>
        FarmFresh Marketplace
    </h2>

    <p>
        Smart Farming Marketplace Connecting Farmers & Buyers
    </p>

    <p>
        farminghelp@gmail.com | +91 9876543210
    </p>

    <div class="socials">

        <a href="#">F</a>
        <a href="#">I</a>
        <a href="#">T</a>
        <a href="#">Y</a>

    </div>

</footer>

</body>
</html>