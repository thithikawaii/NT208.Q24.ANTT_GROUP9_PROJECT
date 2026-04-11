<?php
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

// Kiểm tra nếu dữ liệu không phải là mảng
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Dữ liệu gửi lên không hợp lệ"
    ]);
    exit;
}

$username = trim($input['username'] ?? ''); // Sử dụng trim để loại bỏ khoảng trắng ở đầu và cuối
$password = $input['password'] ?? ''; // Không sử dụng trim cho mật khẩu để giữ nguyên khoảng trắng nếu có

// Kiểm tra nếu có trường nào bị bỏ trống
if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, email, password FROM users WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Kiểm tra 
if ($user = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $user['password'])) { // Sử dụng password_verify để so sánh mật khẩu đã hash
        echo json_encode([
            "success" => true,
            "message" => "Đăng nhập thành công",
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "email" => $user['email']
            ]
        ]);
    } else {
        http_response_code(401); // Mã lỗi 401 Unauthorized
        echo json_encode([
            "success" => false,
            "message" => "Sai tài khoản hoặc mật khẩu"
        ]);
    }
} else {
    http_response_code(401); 
    echo json_encode([
        "success" => false,
        "message" => "Sai tài khoản hoặc mật khẩu"
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>