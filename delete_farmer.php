<?php

include("db.php");

$id = $_GET['id'];

/* FETCH FARMER */

$get_farmer = mysqli_query($conn,"
SELECT *
FROM farmer
WHERE farmer_id='$id'
");

$row = mysqli_fetch_assoc($get_farmer);

/* INSERT HISTORY */

mysqli_query($conn,"

INSERT INTO deleted_farmers(

farmer_name,
email,
mobile,
address,
joined_date,
deleted_date

)

VALUES(

'".$row['full_name']."',
'".$row['email']."',
'".$row['mobile']."',
'".$row['address']."',
'".$row['created_at']."',
NOW()

)

");

/* DELETE FARMER */

mysqli_query($conn,"
DELETE FROM farmer
WHERE farmer_id='$id'
");

header("Location: manage_farmers.php");

?>