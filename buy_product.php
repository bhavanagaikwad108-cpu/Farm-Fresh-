<?php
include("db.php");

$product_id = $_GET['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy Product</title>
</head>
<body>

<h2>Enter Quantity</h2>

<form action="confirm_order.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

    Quantity:
    <input type="number" name="quantity" min="1" required>

    <br><br>
    <input type="submit" value="Confirm Order">
</form>

</body>
</html>