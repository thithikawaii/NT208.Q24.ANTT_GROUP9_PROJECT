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

$title = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');
$status = trim($input['status'] ?? 'todo');
$createdBy = isset($input['createdBy']) ? (int) $input['createdBy'] : null;

if ($title === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Tiêu đề task không được để trống"
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
    "INSERT INTO tasks (title, description, status, created_by) VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $status, $createdBy);

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Không tạo được task"
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Tạo task thành công",
    "taskId" => mysqli_insert_id($conn)
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
