<?php
session_start();
include("db.php");

if(!isset($_SESSION['buyer_id']))
{
    header("Location: buyer_login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

$sql = "SELECT * FROM orders WHERE buyer_id='$buyer_id'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders</title>

<style>
body {
    font-family: Arial;
    background: #f4f4f4;
}

.container {
    width: 90%;
    margin: auto;
    margin-top: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th {
    background: green;
    color: white;
    padding: 10px;
}

td {
    text-align: center;
    padding: 10px;
}

h2 {
    text-align: center;
}

.status-accepted { color: green; font-weight: bold; }
.status-rejected { color: red; font-weight: bold; }
.status-delivered { color: blue; font-weight: bold; }
.status-pending { color: orange; font-weight: bold; }
</style>

</head>

<body>

<div class="container">

<h2>My Orders</h2>

<table border="1">

<tr>
    <th>Order ID</th>
    <th>Product ID</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result))
{
    echo "<tr>
            <td>".$row['order_id']."</td>
            <td>".$row['product_id']."</td>
            <td>".$row['quantity']."</td>
            <td>".$row['total_price']."</td>
            <td>";

    if($row['order_status']=="Accepted")
        echo "<span class='status-accepted'>Accepted by Farmer</span>";

    elseif($row['order_status']=="Rejected")
        echo "<span class='status-rejected'>Rejected by Farmer</span>";

    elseif($row['order_status']=="Delivered")
        echo "<span class='status-delivered'>Delivered</span>";

    else
        echo "<span class='status-pending'>Pending</span>";

    echo "</td>
            <td>".$row['order_date']."</td>
          </tr>";
}
?>

</table>

</div>

</body>
</html>