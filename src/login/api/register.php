<?php
require_once 'config.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

# Kiểm tra dữ liệu đầu vào
if ($username === '' || $password === '') {
    http_response_code(400);
    echo "Username và password không được để trống";
    exit;
}


$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($checkStmt, "s", $username);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

# Kiểm tra nếu username đã tồn tại
if (mysqli_fetch_assoc($checkResult)) {
    http_response_code(409);
    echo "Username đã tồn tại";
    mysqli_stmt_close($checkStmt);
    exit;
}
mysqli_stmt_close($checkStmt);

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
mysqli_stmt_bind_param($insertStmt, "ss", $username, $hashedPassword);

if (mysqli_stmt_execute($insertStmt)) {
    echo "Đăng ký thành công";
} else {
    http_response_code(500);
    echo "Lỗi hệ thống";
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);
?>