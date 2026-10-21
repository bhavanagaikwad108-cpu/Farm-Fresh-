<?php

/*
|--------------------------------------------------------------------------
| Smart Farming System
| Database Connection File
|--------------------------------------------------------------------------
*/

$host = "localhost";
$username = "root";
$password = "";
$database = "farming_system";

/*
|--------------------------------------------------------------------------
| Create Database Connection
|--------------------------------------------------------------------------
*/

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

/*
|--------------------------------------------------------------------------
| Check Connection
|--------------------------------------------------------------------------
*/

if (!$conn)
{
    // Log Error
    error_log(
        "Database Connection Failed: " .
        mysqli_connect_error()
    );

    die(
        "Database Connection Failed!"
    );
}

/*
|--------------------------------------------------------------------------
| Set UTF-8 Character Encoding
|--------------------------------------------------------------------------
*/

mysqli_set_charset($conn, "utf8mb4");

?>