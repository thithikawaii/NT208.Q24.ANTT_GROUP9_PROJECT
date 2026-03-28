<?php
$conn = mysqli_connect("localhost", "root", "", "login_api_demo");

if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}
?>