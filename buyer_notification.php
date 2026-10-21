<?php
include("db.php");
session_start();

// Buyer must be logged in
$buyer_id = $_SESSION['buyer_id'];

// Fetch buyer orders
$sql = "SELECT * FROM orders WHERE buyer_id='$buyer_id' ORDER BY order_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer Notifications</title>

    <style>
        body {
            font-family: Arial;
        }

        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: green;
            color: white;
        }

        h2 {
            text-align: center;
            color: darkgreen;
        }

        .accepted {
            color: green;
            font-weight: bold;
        }

        .rejected {
            color: red;
            font-weight: bold;
        }

        .delivered {
            color: blue;
            font-weight: bold;
        }

        .pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>

<body>

<h2>Your Order Notifications</h2>

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

    // STATUS DISPLAY LOGIC
    if($row['order_status'] == "Accepted") {
        echo "<span class='accepted'>🟢 Accepted</span>";
    }
    elseif($row['order_status'] == "Rejected") {
        echo "<span class='rejected'>🔴 Rejected</span>";
    }
    elseif($row['order_status'] == "Delivered") {
        echo "<span class='delivered'>🚚 Delivered</span>";
    }
    else {
        echo "<span class='pending'>🟡 Pending</span>";
    }

    echo "</td>
            <td>".$row['order_date']."</td>
          </tr>";
}
?>

</table>

</body>
</html>