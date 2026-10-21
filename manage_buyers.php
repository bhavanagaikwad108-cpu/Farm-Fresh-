<?php
session_start();
include("db.php");

/* ADMIN LOGIN CHECK */

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

/* DELETE BUYER */

if(isset($_GET['delete']))
{
    $buyer_id = intval($_GET['delete']);

    mysqli_query($conn,"
    DELETE FROM buyer
    WHERE buyer_id='$buyer_id'
    ");

    header("Location: manage_buyers.php");
    exit();
}

/* SEARCH */

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

/* FETCH BUYERS */

$query = mysqli_query($conn,"
SELECT
buyer.*,
COUNT(orders.order_id) AS total_orders,
IFNULL(SUM(orders.total_price),0) AS total_spent

FROM buyer

LEFT JOIN orders
ON buyer.buyer_id = orders.buyer_id

WHERE
buyer.name LIKE '%$search%'
OR buyer.email LIKE '%$search%'
OR buyer.mobile_no LIKE '%$search%'

GROUP BY buyer.buyer_id

ORDER BY buyer.buyer_id DESC
");

/* TOTAL REPORTS */

$total_buyers = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM buyer
"));

$total_active_buyers = mysqli_num_rows(mysqli_query($conn,"
SELECT DISTINCT buyer_id
FROM orders
"));

$total_orders = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM orders
"));

$total_revenue_query = mysqli_query($conn,"
SELECT SUM(total_price) AS revenue
FROM orders
WHERE order_status='Delivered'
");

$total_revenue_data = mysqli_fetch_assoc($total_revenue_query);

$total_revenue = $total_revenue_data['revenue'];

if($total_revenue == "")
{
    $total_revenue = 0;
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Buyers</title>

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
    background:linear-gradient(180deg,#4c1d95,#7c3aed);
    position:fixed;
    left:0;
    top:0;
    padding:25px 20px;
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
    text-decoration:none;
    color:white;
    padding:15px 18px;
    border-radius:16px;
    margin-bottom:15px;
    transition:0.3s;
}

.menu a:hover,
.active{
    background:rgba(255,255,255,0.12);
    transform:translateX(5px);
}

/* MAIN */

.main{
    margin-left:270px;
    padding:30px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
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
}

/* REPORT CARDS */

.report-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:25px;
    margin-bottom:35px;
}

.report-card{
    background:white;
    padding:28px;
    border-radius:24px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:0.3s;
}

.report-card:hover{
    transform:translateY(-6px);
}

.report-icon{
    width:70px;
    height:70px;
    background:linear-gradient(135deg,#7c3aed,#9333ea);
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
    margin-bottom:18px;
}

.report-card h3{
    color:#64748b;
    font-size:15px;
}

.report-card h2{
    color:#1e1b4b;
    font-size:34px;
    margin-top:10px;
    font-weight:700;
}

/* SEARCH */

.search-box{
    background:white;
    padding:20px;
    border-radius:24px;
    margin-bottom:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.search-form{
    display:flex;
    gap:15px;
}

.search-form input{
    flex:1;
    padding:14px;
    border-radius:14px;
    border:1px solid #ddd;
    outline:none;
}

.search-btn{
    background:#7c3aed;
    color:white;
    border:none;
    padding:14px 22px;
    border-radius:14px;
    font-weight:600;
}

.search-btn:hover{
    background:#6d28d9;
}

/* TABLE */

.table-box{
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    overflow-x:auto;
}

.table th{
    background:#f5f3ff;
    color:#4c1d95;
    padding:16px;
}

.table td{
    padding:16px;
    vertical-align:middle;
}

/* BADGES */

.orders-badge{
    background:#ede9fe;
    color:#6d28d9;
    padding:8px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:600;
}

.spent-badge{
    background:#dcfce7;
    color:#166534;
    padding:8px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:600;
}

/* BUTTONS */

.delete-btn{
    background:#ef4444;
    color:white;
    text-decoration:none;
    padding:10px 16px;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
}

.delete-btn:hover{
    background:#dc2626;
    color:white;
}

/* MOBILE */

@media(max-width:991px)
{
    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main{
        margin-left:0;
    }

    .topbar{
        flex-direction:column;
        gap:15px;
        align-items:flex-start;
    }

    .search-form{
        flex-direction:column;
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

        <a href="manage_buyers.php" class="active">
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

<!-- MAIN -->

<div class="main">

    <!-- TOPBAR -->

    <div class="topbar">

        <h1>Manage Buyers</h1>

        <div class="admin-box">
            Welcome Admin
        </div>

    </div>

    <!-- REPORTS -->

    <div class="report-grid">

        <div class="report-card">

            <div class="report-icon">
                <i class="fa fa-users"></i>
            </div>

            <h3>Total Buyers</h3>

            <h2>
                <?php echo $total_buyers; ?>
            </h2>

        </div>

        <div class="report-card">

            <div class="report-icon">
                <i class="fa fa-user-check"></i>
            </div>

            <h3>Active Buyers</h3>

            <h2>
                <?php echo $total_active_buyers; ?>
            </h2>

        </div>

        <div class="report-card">

            <div class="report-icon">
                <i class="fa fa-cart-shopping"></i>
            </div>

            <h3>Total Orders</h3>

            <h2>
                <?php echo $total_orders; ?>
            </h2>

        </div>

        <div class="report-card">

            <div class="report-icon">
                <i class="fa fa-wallet"></i>
            </div>

            <h3>Total Revenue</h3>

            <h2>
                ₹<?php echo number_format($total_revenue); ?>
            </h2>

        </div>

    </div>

    <!-- SEARCH -->

    <div class="search-box">

        <form method="GET" class="search-form">

            <input
            type="text"
            name="search"
            placeholder="Search buyer by name, email or mobile..."
            value="<?php echo $search; ?>"
            >

            <button type="submit" class="search-btn">
                <i class="fa fa-search"></i>
                Search
            </button>

        </form>

    </div>

    <!-- TABLE -->

    <div class="table-box">

        <table class="table table-bordered align-middle">

            <tr>

                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Action</th>

            </tr>

            <?php

            if(mysqli_num_rows($query) > 0)
            {
                while($row = mysqli_fetch_assoc($query))
                {
            ?>

            <tr>

                <td>
                    #<?php echo $row['buyer_id']; ?>
                </td>

                <td>
                    <b><?php echo htmlspecialchars($row['name']); ?></b>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['email']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['mobile_no']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['address']); ?>
                </td>

                <td>

                    <span class="orders-badge">
                        <?php echo $row['total_orders']; ?> Orders
                    </span>

                </td>

                <td>

                    <span class="spent-badge">
                        ₹<?php echo number_format($row['total_spent']); ?>
                    </span>

                </td>

                <td>

                    <a
                    href="manage_buyer.php?delete=<?php echo $row['buyer_id']; ?>"
                    class="delete-btn"
                    onclick="return confirm('Delete this buyer?')"
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
                    <td colspan='8' style='text-align:center;'>
                        No Buyers Found
                    </td>
                </tr>
                ";
            }

            ?>

        </table>

    </div>

</div>

</body>
</html>