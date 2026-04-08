<?php
require_once 'config.php'; 

$username = trim($_POST['username'] ?? ''); // Lấy username từ request, loại bỏ khoảng trắng
$password = $_POST['password'] ?? ''; // Lấy password từ request, nếu không tồn tại thì gán giá trị rỗng

// Kiểm tra nếu username hoặc password bị bỏ trống, trả về lỗi 400 Bad Request
if ($username === '' || $password === '') {
    http_response_code(400);
    echo "Username và password không được để trống";
    exit;
}

// Chuẩn bị câu truy vấn để lấy thông tin người dùng dựa trên username
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username); // Liên kết tham số username vào câu truy vấn
mysqli_stmt_execute($stmt); // Thực thi câu truy vấn
$result = mysqli_stmt_get_result($stmt); // Lấy kết quả trả về từ câu truy vấn

//Logic kiểm tra nếu tìm thấy người dùng và mật khẩu khớp, trả về thành công, ngược lại trả về lỗi 401 Unauthorized
if ($user = mysqli_fetch_assoc($result)) { 
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