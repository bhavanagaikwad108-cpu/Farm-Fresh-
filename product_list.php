<?php
include("db.php");

// Fetch all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
<a href="buy_product.php?id=<?php echo $row['id']; ?>">Buy</a>
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <style>
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

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            color: darkgreen;
        }

        a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>

<body>

<h2>Available Products</h2>

<table border="1">
<tr>
    <th>ID</th>
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
                <a href='buy_product.php?product_id=".$row['id']."'>
                    Buy Now
                </a>
            </td>
          </tr>";
}
?>

</table>

</body>
</html>