<?php
include 'db.php';

function getReport($conn, $table)
{
    $daily = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM $table
        WHERE DATE(created_at)=CURDATE()
    "))['total'];

    $weekly = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM $table
        WHERE YEARWEEK(created_at,1)=YEARWEEK(CURDATE(),1)
    "))['total'];

    $monthly = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM $table
        WHERE MONTH(created_at)=MONTH(CURDATE())
        AND YEAR(created_at)=YEAR(CURDATE())
    "))['total'];

    $yearly = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM $table
        WHERE YEAR(created_at)=YEAR(CURDATE())
    "))['total'];

    return [
        'daily' => $daily,
        'weekly' => $weekly,
        'monthly' => $monthly,
        'yearly' => $yearly
    ];
}

$farmer  = getReport($conn, "farmer");
$buyer  = getReport($conn, "buyer");
$product = getReport($conn, "product");
$orders   = getReport($conn, "orders");
$delivery = getReport($conn, "delivery");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Reports</title>

<style>
body{
    font-family:Arial,sans-serif;
    background:#f4f6f9;
    margin:20px;
}

h1{
    text-align:center;
    color:#2e7d32;
}

.report-container{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.card h2{
    color:#2e7d32;
    margin-bottom:15px;
}

.card p{
    font-size:16px;
    margin:10px 0;
}
</style>

</head>
<body>

<h1>Admin Reports Dashboard</h1>

<div class="report-container">

    <div class="card">
        <h2>Farmers</h2>
        <p>Today: <?php echo $farmers['daily']; ?></p>
        <p>This Week: <?php echo $farmers['weekly']; ?></p>
        <p>This Month: <?php echo $farmers['monthly']; ?></p>
        <p>This Year: <?php echo $farmers['yearly']; ?></p>
    </div>

    <div class="card">
        <h2>Buyers</h2>
        <p>Today: <?php echo $buyers['daily']; ?></p>
        <p>This Week: <?php echo $buyers['weekly']; ?></p>
        <p>This Month: <?php echo $buyers['monthly']; ?></p>
        <p>This Year: <?php echo $buyers['yearly']; ?></p>
    </div>

    <div class="card">
        <h2>Products</h2>
        <p>Today: <?php echo $products['daily']; ?></p>
        <p>This Week: <?php echo $products['weekly']; ?></p>
        <p>This Month: <?php echo $products['monthly']; ?></p>
        <p>This Year: <?php echo $products['yearly']; ?></p>
    </div>

    <div class="card">
        <h2>Orders</h2>
        <p>Today: <?php echo $orders['daily']; ?></p>
        <p>This Week: <?php echo $orders['weekly']; ?></p>
        <p>This Month: <?php echo $orders['monthly']; ?></p>
        <p>This Year: <?php echo $orders['yearly']; ?></p>
    </div>

    <div class="card">
        <h2>Delivery</h2>
        <p>Today: <?php echo $delivery['daily']; ?></p>
        <p>This Week: <?php echo $delivery['weekly']; ?></p>
        <p>This Month: <?php echo $delivery['monthly']; ?></p>
        <p>This Year: <?php echo $delivery['yearly']; ?></p>
    </div>

</div>

</body>
</html>