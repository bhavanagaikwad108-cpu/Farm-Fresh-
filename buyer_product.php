<?php
include("db.php");
session_start();

// Get all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy Products</title>

    <style>
        body {
            font-family: Arial;
        }

        table {
            width: 90%;
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

        a {
            text-decoration: none;
            background-color: green;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        a:hover {
            background-color: darkgreen;
        }
    </style>
</head>

<body>

<h2>Available Farm Products</h2>

<table border="1">
<tr>
    <th>Product ID</th>
    <th>Farmer ID</th>
    <th>Product Name</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Description</th>
    <th>Action</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result))
{
    echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['farmer_id']."</td>
            <td>".$row['product_name']."</td>
            <td>".$row['price']."</td>
            <td>".$row['quantity']."</td>
            <td>".$row['description']."</td>

            <td>
                <a href='buy_product.php?product_id=".$row['id']."'>Buy Now</a>
            </td>
          </tr>";
}
?>

</table>

</body>
</html>