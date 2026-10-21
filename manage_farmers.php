<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

$farmers = mysqli_query($conn,"
SELECT *
FROM farmer
ORDER BY farmer_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Farmers</title>

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
    color:#0f172a;
}

/* PAGE CONTAINER */

.page-container{
    padding:35px;
}

/* TOP BAR */

.top-bar{
    background:white;
    border-radius:24px;
    padding:25px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
}

.title-box h1{
    font-size:34px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:6px;
}

.title-box p{
    color:#64748b;
    font-size:15px;
}

/* DASHBOARD BUTTON */

.dashboard-btn{
    background:#2563eb;
    color:white;
    text-decoration:none;
    padding:14px 22px;
    border-radius:14px;
    font-weight:600;
    transition:0.3s;
    display:flex;
    align-items:center;
    gap:10px;
}

.dashboard-btn:hover{
    background:#1d4ed8;
    color:white;
}

/* TABLE BOX */

.table-box{
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow:0 4px 18px rgba(0,0,0,0.05);
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
    color:#475569;
    font-size:14px;
    font-weight:600;
    border:none;
}

.table td{
    padding:18px 16px;
    vertical-align:middle;
    border-bottom:1px solid #e2e8f0;
    font-size:14px;
}

/* FARMER PROFILE */

.profile-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.profile-img{
    width:60px;
    height:60px;
}

/* REAL FARMER IMAGE */

.farmer-photo{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #2563eb;
}

/* DEFAULT PROFILE */

.default-profile{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#2563eb;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    font-weight:700;
}

/* BADGE */

.join-badge{
    background:#dcfce7;
    color:#166534;
    padding:8px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:600;
}

/* ACTION BUTTON */

.delete-btn{
    background:#ef4444;
    color:white;
    text-decoration:none;
    padding:10px 16px;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
    transition:0.3s;
}

.delete-btn:hover{
    background:#dc2626;
    color:white;
}

/* EMPTY BOX */

.empty-box{
    text-align:center;
    padding:60px 20px;
}

.empty-box i{
    font-size:70px;
    color:#cbd5e1;
}

.empty-box h3{
    margin-top:15px;
    font-size:28px;
}

.empty-box p{
    color:#64748b;
    margin-top:8px;
}

/* RESPONSIVE */

@media(max-width:768px){

    .top-bar{
        flex-direction:column;
        gap:20px;
        align-items:flex-start;
    }

    .title-box h1{
        font-size:28px;
    }

}

</style>

</head>

<body>

<div class="page-container">

    <!-- TOP BAR -->

    <div class="top-bar">

        <div class="title-box">

            <h1>
                👨‍🌾 Farmers Management
            </h1>

            <p>
                Manage all registered farmers professionally from admin panel.
            </p>

        </div>

        <a href="admin_dashboard.php" class="dashboard-btn">

            <i class="fa fa-arrow-left"></i>

            Back To Dashboard

        </a>

    </div>

    <!-- TABLE -->

    <div class="table-box">

    <?php if(mysqli_num_rows($farmers) > 0){ ?>

        <table class="table">

            <thead>

                <tr>

                    <th>Farmer</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Joined Date</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

            <?php while($row = mysqli_fetch_assoc($farmers)) { ?>

                <tr>

                    <td>

                        <div class="profile-img">

<?php if(!empty($row['image'])) { ?>

    <img 
    src="uploads/farmers/<?php echo $row['image']; ?>"
    class="farmer-photo">

<?php } else { ?>

    <div class="default-profile">

        <?php
        echo strtoupper(substr($row['full_name'],0,1));
        ?>

    </div>

<?php } ?>

</div>

                            <div>

                                <b>
                                    <?php echo $row['full_name']; ?>
                                </b>

                                <br>

                                <small style="color:#64748b;">

                                    Farmer ID:
                                    <?php echo $row['farmer_id']; ?>

                                </small>

                            </div>

                        </div>

                    </td>

                    <td>
                        <?php echo $row['email']; ?>
                    </td>

                    <td>
                        <?php echo $row['mobile']; ?>
                    </td>

                    <td>
                        <?php echo $row['farm_address']; ?>
                    </td>

                    <td>

                        <span class="join-badge">

                            <?php
                            echo date("d M Y",strtotime($row['created_at']));
                            ?>

                        </span>

                    </td>

                    <td>

                        <a href="delete_farmer.php?id=<?php echo $row['farmer_id']; ?>"
                        class="delete-btn">

                            Delete

                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    <?php } else { ?>

        <div class="empty-box">

            <i class="fa fa-users"></i>

            <h3>No Farmers Found</h3>

            <p>
                Registered farmers will appear here.
            </p>

        </div>

    <?php } ?>

    </div>

</div>

</body>
</html>