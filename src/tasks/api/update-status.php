<?php
require_once '../../login/api/config.php';

$allowedStatuses = ['todo', 'doing', 'done'];
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
$status = trim($input['status'] ?? '');

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID task không hợp lệ"
    ]);
    exit;
}

if (!in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Trạng thái task không hợp lệ"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE tasks SET status = ? WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) < 0) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Không đổi được trạng thái task"
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Cập nhật trạng thái task thành công"
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
