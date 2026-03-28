<?php
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$username = isset($data["username"]) ? trim($data["username"]) : "";
$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? trim($data["password"]) : "";
$confirmPassword = isset($data["confirmPassword"]) ? trim($data["confirmPassword"]) : "";

if ($username == "" || $email == "" || $password == "" || $confirmPassword == "") {
    echo json_encode([
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit();
}

if ($password != $confirmPassword) {
    echo json_encode([
        "success" => false,
        "message" => "Mật khẩu nhập lại không khớp"
    ]);
    exit();
}

$check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Tên đăng nhập hoặc email đã tồn tại"
    ]);
    exit();
}

$sql = "INSERT INTO users(username, email, password) VALUES ('$username', '$email', '$password')";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "success" => true,
        "message" => "Đăng ký thành công"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Đăng ký thất bại"
    ]);
}
?>