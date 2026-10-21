<?php
session_start();
include 'db.php';

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

/* FETCH ORDERS */
$orders = mysqli_query($conn,"
SELECT orders.*, product.product_name, product.image
FROM orders
JOIN product ON orders.product_id = product.product_id
WHERE orders.farmer_id='$farmer_id'
ORDER BY orders.order_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Orders</title>

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
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
}

/* CONTAINER */

.orders-container{
    padding:30px;
}

/* TOP BAR */

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.top-bar h2{
    font-size:30px;
    font-weight:700;
    color:#0f172a;
}

.back-btn{
    background:#22c55e;
    color:white;
    text-decoration:none;
    padding:12px 18px;
    border-radius:14px;
    font-size:14px;
    font-weight:600;
    transition:0.3s;
}

.back-btn:hover{
    background:#16a34a;
    color:white;
}

/* ORDER BOX */

.orders-box{
    background:white;
    border-radius:24px;
    padding:20px;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    overflow-x:auto;
}

/* TABLE */

.table{
    margin:0;
}

.table thead{
    background:#f8fafc;
}

.table th{
    padding:16px;
    font-size:14px;
    color:#334155;
    font-weight:600;
}

.table td{
    padding:16px;
    vertical-align:middle;
    font-size:14px;
    color:#0f172a;
}

/* PRODUCT IMAGE */

.product-img{
    width:70px;
    height:70px;
    border-radius:16px;
    object-fit:cover;
    border:2px solid #22c55e;
}

/* STATUS */

.status-pending{
    background:#fef3c7;
    color:#92400e;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-accepted{
    background:#dbeafe;
    color:#1d4ed8;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-delivered{
    background:#dcfce7;
    color:#166534;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-rejected{
    background:#fee2e2;
    color:#991b1b;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-picked{
    background:#fef9c3;
    color:#854d0e;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-out{
    background:#dbeafe;
    color:#1d4ed8;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-ready{
    background:#ede9fe;
    color:#6d28d9;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

/* ACTION BUTTONS */

.action-btn{
    padding:7px 14px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:12px;
    font-weight:600;
    display:inline-block;
    margin-top:8px;
    transition:0.3s;
}

.accept-btn{
    background:#16a34a;
}

.reject-btn{
    background:#dc2626;
}

.deliver-btn{
    background:#2563eb;
}

.action-btn:hover{
    opacity:0.9;
    color:white;
}

/* EMPTY */

.empty-box{
    text-align:center;
    padding:50px 20px;
}

.empty-box i{
    font-size:70px;
    color:#cbd5e1;
}

.empty-box h3{
    margin-top:15px;
    font-size:24px;
    color:#334155;
}

.empty-box p{
    color:#64748b;
    margin-top:5px;
}

/* RESPONSIVE */

@media(max-width:768px){

    .top-bar{
        flex-direction:column;
        gap:15px;
        align-items:flex-start;
    }

    .top-bar h2{
        font-size:24px;
    }

}

</style>

</head>

<body>

<div class="orders-container">

    <!-- TOP BAR -->

    <div class="top-bar">

        <h2>📦 My Orders</h2>

        <a href="farmer_dashboard.php" class="back-btn">
            <i class="fa fa-arrow-left"></i>
            Back Dashboard
        </a>

    </div>

    <!-- ORDERS -->

    <div class="orders-box">

    <?php if(mysqli_num_rows($orders) > 0){ ?>

        <table class="table align-middle">

            <thead>

                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status / Actions</th>
                    <th>Order Date</th>
                </tr>

            </thead>

            <tbody>

            <?php while($row = mysqli_fetch_assoc($orders)) { ?>

                <tr>

                    <td>

                        <?php if(!empty($row['image'])) { ?>

                            <img 
                            src="uploads/products/<?php echo $row['image']; ?>" 
                            class="product-img">

                        <?php } ?>

                    </td>

                    <td>
                        <b>
                        <?php echo htmlspecialchars($row['product_name']); ?>
                        </b>
                    </td>

                    <td>
                        <?php echo $row['quantity']; ?> KG
                    </td>

                    <td>
                        ₹<?php echo number_format($row['total_price']); ?>
                    </td>

                    <td>

<?php if($row['order_status'] == 'Pending'){ ?>

    <span class="status-pending">
        Pending
    </span>

    <br>

    <a href="update_order.php?id=<?php echo $row['order_id']; ?>&status=Accepted"
    class="action-btn accept-btn">
        Accept
    </a>

    <a href="update_order.php?id=<?php echo $row['order_id']; ?>&status=Rejected"
    class="action-btn reject-btn">
        Reject
    </a>

<?php } elseif($row['order_status'] == 'Accepted'){ ?>

    <span class="status-accepted">
        Accepted
    </span>

    <br>

    <a href="mark_ready.php?order_id=<?php echo $row['order_id']; ?>"
    class="action-btn deliver-btn">
        Ready For Pickup
    </a>

<?php } elseif($row['order_status'] == 'Ready For Pickup'){ ?>

    <span class="status-ready">
        Ready For Pickup
    </span>

<?php } elseif($row['order_status'] == 'Picked Up'){ ?>

    <span class="status-picked">
        Picked Up
    </span>

<?php } elseif($row['order_status'] == 'Out For Delivery'){ ?>

    <span class="status-out">
        Out For Delivery
    </span>

<?php } elseif($row['order_status'] == 'Delivered'){ ?>

    <span class="status-delivered">
        Delivered
    </span>

<?php } elseif($row['order_status'] == 'Rejected'){ ?>

    <span class="status-rejected">
        Rejected
    </span>

<?php } ?>

                    </td>

                    <td>
                        <?php echo date("d M Y", strtotime($row['order_date'])); ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    <?php } else { ?>

        <div class="empty-box">

            <i class="fa fa-box-open"></i>

            <h3>No Orders Yet</h3>

            <p>
                Orders received from buyers will appear here.
            </p>

        </div>

    <?php } ?>

    </div>

</div>

</body>
</html>