<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "bite_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>