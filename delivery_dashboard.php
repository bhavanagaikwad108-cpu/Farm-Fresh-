<?php
session_start();
include('db.php');

/*
|--------------------------------------------------------------------------
| DELIVERY BOY LOGIN CHECK
|--------------------------------------------------------------------------
*/

if(!isset($_SESSION['delivery_id']))
{
    header("Location: delivery_login.php");
    exit();
}

$delivery_id = $_SESSION['delivery_id'];

/*
|--------------------------------------------------------------------------
| DELIVERY BOY DETAILS
|--------------------------------------------------------------------------
*/

$delivery_query = mysqli_query($conn,"
SELECT *
FROM delivery_boy
WHERE delivery_id='$delivery_id'
");

$delivery = mysqli_fetch_assoc($delivery_query);

/*
|--------------------------------------------------------------------------
| TOTAL ASSIGNED ORDERS
|--------------------------------------------------------------------------
*/

$total_orders_query = mysqli_query($conn,"
SELECT COUNT(*) AS total_orders
FROM orders
WHERE delivery_boy_id='$delivery_id'
");

$total_orders = mysqli_fetch_assoc($total_orders_query);

/*
|--------------------------------------------------------------------------
| PICKED UP ORDERS
|--------------------------------------------------------------------------
*/

$picked_query = mysqli_query($conn,"
SELECT COUNT(*) AS picked_orders
FROM orders
WHERE delivery_boy_id='$delivery_id'
AND delivery_status='Picked Up'
");

$picked_orders = mysqli_fetch_assoc($picked_query);

/*
|--------------------------------------------------------------------------
| OUT FOR DELIVERY
|--------------------------------------------------------------------------
*/

$out_query = mysqli_query($conn,"
SELECT COUNT(*) AS out_orders
FROM orders
WHERE delivery_boy_id='$delivery_id'
AND delivery_status='Out For Delivery'
");

$out_orders = mysqli_fetch_assoc($out_query);

/*
|--------------------------------------------------------------------------
| DELIVERED ORDERS
|--------------------------------------------------------------------------
*/

$delivered_query = mysqli_query($conn,"
SELECT COUNT(*) AS delivered_orders
FROM orders
WHERE delivery_boy_id='$delivery_id'
AND delivery_status='Delivered'
");

$delivered_orders = mysqli_fetch_assoc($delivered_query);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Delivery Dashboard</title>

    <style>

        *
        {
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body
        {
            background:#f4f7fb;
        }

        .container
        {
            width:95%;
            margin:30px auto;
        }

        .top-bar
        {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:30px;
        }

        .title
        {
            font-size:35px;
            font-weight:bold;
            color:#2c3e50;
        }

        .logout-btn
        {
            text-decoration:none;
            background:#dc3545;
            color:white;
            padding:12px 20px;
            border-radius:10px;
            font-weight:bold;
            transition:0.3s;
        }

        .logout-btn:hover
        {
            background:#b52b37;
        }

        .profile-card
        {
            background:white;
            padding:25px;
            border-radius:20px;
            margin-bottom:30px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
        }

        .profile-card h2
        {
            color:#6c5ce7;
            margin-bottom:15px;
        }

        .profile-card p
        {
            margin-bottom:10px;
            font-size:17px;
            color:#333;
        }

        .dashboard-grid
        {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
            gap:20px;
        }

        .card
        {
            background:white;
            padding:30px;
            border-radius:20px;
            text-align:center;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
            transition:0.3s;
        }

        .card:hover
        {
            transform:translateY(-5px);
        }

        .card h3
        {
            font-size:22px;
            margin-bottom:15px;
            color:#2c3e50;
        }

        .card p
        {
            font-size:35px;
            font-weight:bold;
            color:#6c5ce7;
        }

        .orders-btn
        {
            display:inline-block;
            margin-top:15px;
            text-decoration:none;
            background:#28a745;
            color:white;
            padding:10px 18px;
            border-radius:10px;
            font-weight:bold;
        }

        .orders-btn:hover
        {
            background:#218838;
        }

        @media(max-width:768px)
        {
            .top-bar
            {
                flex-direction:column;
                gap:15px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="top-bar">

        <div class="title">
            Delivery Dashboard
        </div>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

    <div class="profile-card">

        <h2>
            Delivery Boy Profile
        </h2>

        <p>
            <b>Name:</b>
            <?php echo $delivery['full_name']; ?>
        </p>

        <p>
            <b>Mobile:</b>
            <?php echo $delivery['mobile']; ?>
        </p>

        <p>
            <b>Email:</b>
            <?php echo $delivery['email']; ?>
        </p>

        <p>
            <b>Vehicle Number:</b>
            <?php echo $delivery['vehicle_number']; ?>
        </p>

        <p>
            <b>Address:</b>
            <?php echo $delivery['address']; ?>
        </p>

        <p>
            <b>Status:</b>
            <?php echo $delivery['status']; ?>
        </p>

    </div>

    <div class="dashboard-grid">

        <div class="card">

            <h3>
                Total Assigned Orders
            </h3>

            <p>
                <?php echo $total_orders['total_orders']; ?>
            </p>

            <a href="delivery_order.php" class="orders-btn">
                View Orders
            </a>

        </div>

        <div class="card">

            <h3>
                Picked Up Orders
            </h3>

            <p>
                <?php echo $picked_orders['picked_orders']; ?>
            </p>

        </div>

        <div class="card">

            <h3>
                Out For Delivery
            </h3>

            <p>
                <?php echo $out_orders['out_orders']; ?>
            </p>

        </div>

        <div class="card">

            <h3>
                Delivered Orders
            </h3>

            <p>
                <?php echo $delivered_orders['delivered_orders']; ?>
            </p>

        </div>

    </div>

</div>

</body>
</html>