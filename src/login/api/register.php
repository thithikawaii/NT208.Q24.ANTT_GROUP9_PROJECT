<?php
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true); // Đọc dữ liệu JSON từ body của request


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
$email = trim($input['email'] ?? ''); 
$password = $input['password'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';

// Kiểm tra nếu có trường nào bị bỏ trống
if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
    http_response_code(400); // Mã lỗi 400 Bad Request
    echo json_encode([ // Trả về phản hồi JSON với thông báo lỗi
        "success" => false,
        "message" => "Vui lòng nhập đầy đủ thông tin"
    ]);
    exit;
}

// Kiểm tra định dạng email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email không hợp lệ"
    ]);
    exit;
}

// Kiểm tra nếu mật khẩu và xác nhận mật khẩu không khớp
if ($password !== $confirmPassword) {
    http_response_code(400); 
    echo json_encode([
        "success" => false,
        "message" => "Mật khẩu nhập lại không khớp"
    ]);
    exit;
}

// Kiểm tra nếu username đã tồn tại
$checkUserStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($checkUserStmt, "s", $username);
mysqli_stmt_execute($checkUserStmt);
$userResult = mysqli_stmt_get_result($checkUserStmt);

// Nếu có kết quả trả về nghĩa là username đã tồn tại trong database
if (mysqli_fetch_assoc($userResult)) {
    mysqli_stmt_close($checkUserStmt);
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Username đã tồn tại"
    ]);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_close($checkUserStmt);

// Kiểm tra nếu email đã tồn tại
$checkEmailStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
mysqli_stmt_execute($checkEmailStmt);
$emailResult = mysqli_stmt_get_result($checkEmailStmt);

if (mysqli_fetch_assoc($emailResult)) {
    mysqli_stmt_close($checkEmailStmt);
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email đã được sử dụng"
    ]);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_close($checkEmailStmt);

// Hash mật khẩu trước khi lưu vào database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Câu lệnh SQL để chèn dữ liệu vào bảng users
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
    http_response_code(500); // Mã lỗi 500 Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Lỗi hệ thống"
    ]);
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);
?>