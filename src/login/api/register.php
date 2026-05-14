<?php

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Dữ liệu gửi lên không hợp lệ"
    ]);
    exit;
}

$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';

if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email không hợp lệ"
    ]);
    exit;
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/', $password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Mật khẩu phải có ít nhất 8 ký tự, chữ hoa, chữ thường, số và ký tự đặc biệt"
    ]);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Mật khẩu nhập lại không khớp"
    ]);
    exit;
}

$checkUserStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($checkUserStmt, "s", $username);
mysqli_stmt_execute($checkUserStmt);
$userResult = mysqli_stmt_get_result($checkUserStmt);

if (mysqli_fetch_assoc($userResult)) {
    mysqli_stmt_close($checkUserStmt);
    mysqli_close($conn);
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Username đã tồn tại"
    ]);
    exit;
}
mysqli_stmt_close($checkUserStmt);

$checkEmailStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
mysqli_stmt_execute($checkEmailStmt);
$emailResult = mysqli_stmt_get_result($checkEmailStmt);

if (mysqli_fetch_assoc($emailResult)) {
    mysqli_stmt_close($checkEmailStmt);
    mysqli_close($conn);
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email đã được sử dụng"
    ]);
    exit;
}
mysqli_stmt_close($checkEmailStmt);

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode([
        "success" => true,
        "message" => "Đăng ký thành công"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Lỗi hệ thống"
    ]);
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);
?>
