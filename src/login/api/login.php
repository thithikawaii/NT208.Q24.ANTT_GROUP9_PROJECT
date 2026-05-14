<?php

require_once 'config.php';
require_once 'AuthService.php';

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
$password = $input['password'] ?? '';

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userFromDB = mysqli_fetch_assoc($result) ?: null;

$auth = new AuthService();
$loginResult = $auth->verify($username, $password, $userFromDB);

http_response_code($loginResult['status']);
echo json_encode($loginResult['data']);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
