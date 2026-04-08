<?php

# Cấu hình kết nối cơ sở dữ liệu;
# Sử dụng biến môi trường để linh hoạt cấu hình trong các môi trường khác nhau (local, staging, production)
$dbHost = getenv('DB_HOST') ?: 'db'; # Sử dụng 'db' làm hostname mặc định, phù hợp với cấu hình Docker Compose
$dbName = getenv('DB_NAME') ?: 'login_api_demo';
$dbUser = getenv('DB_USER') ?: 'app_user';
$dbPass = getenv('DB_PASS') ?: '';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    http_response_code(500);
    die("Database connection failed");
}
?>