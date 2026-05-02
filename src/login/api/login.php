<?php
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Du lieu gui len khong hop le"
    ]);
    exit;
}

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Vui long nhap day du thong tin"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Dang nhap thanh cong",
            "user" => [
                "id" => (int) $user['id'],
                "username" => $user['username'],
                "email" => $user['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Sai tai khoan hoac mat khau"
        ]);
    }
} else {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Sai tai khoan hoac mat khau"
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
