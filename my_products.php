<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

$sql = "SELECT * FROM product WHERE farmer_id='$farmer_id' ORDER BY product_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Products</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body{
    background:#f1f5f9;
    font-family:Poppins;
}

.table-box{
    background:white;
    padding:25px;
    margin:30px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.06);
}

img{
    width:90px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
}
img:hover{
    transform:translateY(-6px);
}
</style>

</head>

<body>

<div class="table-box">

<h3>My Products</h3>

<table class="table table-bordered align-middle">

<thead>
<tr>
<th>Image</th>
<th>Name</th>
<th>Category</th>
<th>Quantity</th>
<th>Price</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td>
<img src="uploads/products/<?php echo $row['image']; ?>">
</td>

<td><?php echo $row['product_name']; ?></td>

<td><?php echo $row['category']; ?></td>

<td><?php echo $row['quantity']; ?> KG</td>

<td>₹<?php echo $row['price']; ?> / KG</td>

<td>

<!-- SAME EDIT/DELETE (NO CHANGE) -->
<a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-primary btn-sm">Edit</a>

<a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</body>
</html>