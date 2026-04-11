<?php
require_once 'config.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo "Username và password không được để trống";
    exit;
}

// Tìm user theo username
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    // So sánh password người dùng nhập với hash trong DB
    if (password_verify($password, $user['password'])) {
        echo "Đăng nhập thành công";
    } else {
        http_response_code(401);
        echo "Sai tài khoản hoặc mật khẩu";
    }
} else {
    http_response_code(401);
    echo "Sai tài khoản hoặc mật khẩu";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>