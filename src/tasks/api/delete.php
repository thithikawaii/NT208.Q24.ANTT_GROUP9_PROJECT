<?php
require_once '../../login/api/config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Dữ liệu gửi lên không hợp lệ"
    ]);
    exit;
}

$id = isset($input['id']) ? (int) $input['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID task không hợp lệ"
    ]);
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) <= 0) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Không tìm thấy task để xóa"
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Xóa task thành công"
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
