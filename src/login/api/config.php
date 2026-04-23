<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Bật chế độ báo lỗi của MySQLi để dễ dàng debug

header('Content-Type: application/json; charset=utf-8'); // Đặt header để trả về JSON và hỗ trợ UTF-8

$dbHost = getenv('DB_HOST') ?: 'localhost'; // Lấy DB_HOST từ biến môi trường, nếu không có thì mặc định là 'localhost'
$dbName = getenv('DB_NAME') ?: 'login_api_demo'; // Lấy DB_NAME từ biến môi trường, nếu không có thì mặc định là 'login_api_demo'
$dbUser = getenv('DB_USER') ?: 'root'; // Lấy DB_USER từ biến môi trường, nếu không có thì mặc định là 'root'

/*
  Ưu tiên DB_PASS theo Docker.
  Hỗ trợ thêm DB_PASSWORD để tránh lệch tên biến môi trường.
*/
$dbPass = getenv('DB_PASS');
// Nếu DB_PASS không có, thử lấy DB_PASSWORD
if ($dbPass === false) {
    $dbPass = getenv('DB_PASSWORD');
}
$dbPass = $dbPass ?: ''; // Nếu vẫn không có, mặc định là chuỗi rỗng

// Kết nối database với mysqli và xử lý lỗi kết nối
try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName); 
    $conn->set_charset("utf8mb4"); // Đặt charset để hỗ trợ UTF-8
} 
// Bắt lỗi kết nối và trả về JSON lỗi
catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Không kết nối được database"
    ]);
    exit;
}
?>