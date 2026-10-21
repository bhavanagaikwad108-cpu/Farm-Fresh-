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

$message = "";

/*
|--------------------------------------------------------------------------
| UPDATE DELIVERY STATUS
|--------------------------------------------------------------------------
*/

if(isset($_POST['update_status']))
{
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $allowed_status = array(
        "Picked Up",
        "Out For Delivery",
        "Delivered"
    );

    if(in_array($status, $allowed_status))
    {
        $update = mysqli_query($conn,"
        UPDATE orders
        SET order_status='$status'
        WHERE order_id='$order_id'
        ");

        if($update)
        {
            $message = "Delivery status updated successfully.";
        }
        else
        {
            $message = "Failed to update status.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Delivery Orders</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            margin-bottom:20px;
        }

        .title
        {
            font-size:32px;
            font-weight:bold;
            color:#2c3e50;
        }

        .dashboard-btn
        {
            text-decoration:none;
            background:#6c5ce7;
            color:white;
            padding:12px 20px;
            border-radius:10px;
            font-weight:bold;
            transition:0.3s;
        }

        .dashboard-btn:hover
        {
            background:#4b3fc9;
        }

        .message
        {
            background:#d4edda;
            color:#155724;
            padding:15px;
            border-radius:10px;
            margin-bottom:20px;
            font-weight:bold;
        }

        table
        {
            width:100%;
            border-collapse:collapse;
            background:white;
            border-radius:15px;
            overflow:hidden;
            box-shadow:0 4px 15px rgba(0,0,0,0.1);
        }

        table th
        {
            background:#6c5ce7;
            color:white;
            padding:15px;
            text-align:center;
        }

        table td
        {
            padding:15px;
            text-align:center;
            border-bottom:1px solid #eee;
        }

        tr:hover
        {
            background:#f9f9f9;
        }

        .status
        {
            padding:8px 14px;
            border-radius:20px;
            font-size:14px;
            font-weight:bold;
            display:inline-block;
        }

        .assigned
        {
            background:#cce5ff;
            color:#004085;
        }

        .picked
        {
            background:#fff3cd;
            color:#856404;
        }

        .out
        {
            background:#d1ecf1;
            color:#0c5460;
        }

        .delivered
        {
            background:#d4edda;
            color:#155724;
        }

        select
        {
            padding:10px;
            border-radius:8px;
            border:1px solid #ccc;
        }

        .update-btn
        {
            background:#28a745;
            color:white;
            border:none;
            padding:10px 16px;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
            margin-top:10px;
        }

        .update-btn:hover
        {
            background:#218838;
        }

        @media(max-width:900px)
        {
            table
            {
                display:block;
                overflow-x:auto;
            }

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
            Delivery Orders
        </div>

        <a href="delivery_dashboard.php" class="dashboard-btn">
            Dashboard
        </a>

    </div>

    <?php
    if($message != "")
    {
        echo "<div class='message'>$message</div>";
    }
    ?>

    <table>

        <tr>
            <th>Order ID</th>
            <th>Buyer</th>
            <th>Farmer</th>
            <th>Product</th>
            <th>Total Price</th>
            <th>Address</th>
            <th>Status</th>
            <th>Update Status</th>
        </tr>

        <?php

        $orders = mysqli_query($conn,"
        SELECT
            orders.*,
            buyer.name AS buyer_name,
            buyer.address AS buyer_address,
            farmer.full_name AS farmer_name,
            product.product_name

        FROM orders

        LEFT JOIN buyer
        ON orders.buyer_id = buyer.buyer_id

        LEFT JOIN farmer
        ON orders.farmer_id = farmer.farmer_id

        LEFT JOIN product
        ON orders.product_id = product.product_id

        WHERE orders.delivery_boy_id='$delivery_id'

        ORDER BY orders.order_id DESC
        ");

        if(mysqli_num_rows($orders) > 0)
        {
            while($row = mysqli_fetch_assoc($orders))
            {
        ?>

        <tr>

            <td>
                <?php echo $row['order_id']; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['buyer_name']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['farmer_name']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['product_name']); ?>
            </td>

            <td>
                ₹<?php echo $row['total_price']; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['buyer_address']); ?>
            </td>

            <td>

                <?php

                if($row['order_status'] == 'Ready For Pickup')
                {
                    echo "<span class='status assigned'>Ready For Pickup</span>";
                }
                elseif($row['order_status'] == 'Picked Up')
                {
                    echo "<span class='status picked'>Picked Up</span>";
                }
                elseif($row['order_status'] == 'Out For Delivery')
                {
                    echo "<span class='status out'>Out For Delivery</span>";
                }
                elseif($row['order_status'] == 'Delivered')
                {
                    echo "<span class='status delivered'>Delivered</span>";
                }
                else
                {
                    echo "<span class='status assigned'>Pending</span>";
                }

                ?>

            </td>

            <td>

                <form method="POST">

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?php echo $row['order_id']; ?>"
                    >

                    <select name="status" required>

                        <option value="">
                            Select Status
                        </option>

                        <option value="Picked Up">
                            Picked Up
                        </option>

                        <option value="Out For Delivery">
                            Out For Delivery
                        </option>

                        <option value="Delivered">
                            Delivered
                        </option>

                    </select>

                    <br>

                    <button
                        type="submit"
                        name="update_status"
                        class="update-btn"
                    >
                        Update
                    </button>

                </form>

            </td>

        </tr>

        <?php
            }
        }
        else
        {
            echo "
            <tr>
                <td colspan='8'>No Assigned Orders Found</td>
            </tr>
            ";
        }

        ?>

    </table>

</div>

</body>
</html>