<?php
header("Content-Type: application/json; charset=UTF-8");
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$username = isset($data["username"]) ? trim($data["username"]) : "";
$password = isset($data["password"]) ? trim($data["password"]) : "";

if ($username == "" || $password == "") {
    echo json_encode([
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit();
}

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi truy vấn: " . mysqli_error($conn)
    ]);
    exit();
}

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    echo json_encode([
        "success" => true,
        "message" => "Đăng nhập thành công",
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "email" => $user["email"]
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Sai tên đăng nhập hoặc mật khẩu"
    ]);
}
?>