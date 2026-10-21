<?php
session_start();
include('db.php');

$message = "";

/*
|--------------------------------------------------------------------------
| ASSIGN DELIVERY BOY
|--------------------------------------------------------------------------
*/

if(isset($_POST['assign_delivery']))
{
    $order_id = intval($_POST['order_id']);
    $delivery_boy_id = intval($_POST['delivery_boy_id']);

    $check_order = mysqli_query($conn,"
    SELECT *
    FROM orders
    WHERE order_id='$order_id'
    ");

    if(mysqli_num_rows($check_order) > 0)
    {
        $update = mysqli_query($conn,"
        UPDATE orders
        SET delivery_boy_id='$delivery_boy_id',
            delivery_status='Assigned'
        WHERE order_id='$order_id'
        ");

        if($update)
        {
            $message = "Delivery boy assigned successfully.";
        }
        else
        {
            $message = "Something went wrong.";
        }
    }
    else
    {
        $message = "Order not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Delivery</title>

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

        select
        {
            padding:10px;
            border-radius:8px;
            border:1px solid #ccc;
            width:200px;
        }

        .assign-btn
        {
            background:#28a745;
            color:white;
            border:none;
            padding:10px 18px;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
            transition:0.3s;
        }

        .assign-btn:hover
        {
            background:#218838;
        }

        .status
        {
            padding:8px 14px;
            border-radius:20px;
            font-size:14px;
            font-weight:bold;
            display:inline-block;
        }

        .pending
        {
            background:#ffeeba;
            color:#856404;
        }

        .assigned
        {
            background:#cce5ff;
            color:#004085;
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
            Assign Delivery
        </div>

        <a href="admin_dashboard.php" class="dashboard-btn">
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
            <th>Buyer Name</th>
            <th>Farmer Name</th>
            <th>Product</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Assign Delivery Boy</th>
        </tr>

        <?php

        $orders = mysqli_query($conn,"
        SELECT
            orders.*,
            buyer.name AS buyer_name,
            farmer.full_name AS farmer_name,
            product.product_name,
            delivery_boy.full_name AS delivery_boy_name

        FROM orders

        LEFT JOIN buyer
        ON orders.buyer_id = buyer.buyer_id

        LEFT JOIN farmer
        ON orders.farmer_id = farmer.farmer_id

        LEFT JOIN product
        ON orders.product_id = product.product_id

        LEFT JOIN delivery_boy
        ON orders.delivery_boy_id = delivery_boy.delivery_id

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
                <?php echo $row['buyer_name']; ?>
            </td>

            <td>
                <?php echo $row['farmer_name']; ?>
            </td>

            <td>
                <?php echo $row['product_name']; ?>
            </td>

            <td>
                ₹<?php echo $row['total_price']; ?>
            </td>

            <td>

                <?php

                if(isset($row['order_date']) && $row['order_date'] != "")
                {
                    echo date("d M Y", strtotime($row['order_date']));
                }
                else
                {
                    echo "N/A";
                }

                ?>

            </td>

            <td>

                <?php

                if($row['delivery_status'] == 'Assigned')
                {
                    echo "<span class='status assigned'>Assigned</span>";
                }
                else
                {
                    echo "<span class='status pending'>Pending</span>";
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

                    <select name="delivery_boy_id" required>

                        <option value="">
                            Select Delivery Boy
                        </option>

                        <?php

                        $boys = mysqli_query($conn,"
                        SELECT *
                        FROM delivery_boy
                        ORDER BY delivery_id DESC
                        ");

                        while($boy = mysqli_fetch_assoc($boys))
                        {
                        ?>

                        <option value="<?php echo $boy['delivery_id']; ?>">

                            <?php echo $boy['full_name']; ?>

                        </option>

                        <?php
                        }
                        ?>

                    </select>

                    <br><br>

                    <button
                        type="submit"
                        name="assign_delivery"
                        class="assign-btn"
                    >
                        Assign
                    </button>

                </form>

                <?php

                if($row['delivery_boy_name'] != "")
                {
                    echo "<br><br>";

                    echo "<b>Assigned To:</b> ";

                    echo $row['delivery_boy_name'];
                }

                ?>

            </td>

        </tr>

        <?php

            }
        }
        else
        {
            echo "
            <tr>
                <td colspan='8'>No Orders Found</td>
            </tr>
            ";
        }

        ?>

    </table>

</div>

</body>
</html>