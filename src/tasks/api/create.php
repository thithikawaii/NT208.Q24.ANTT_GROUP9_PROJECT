<?php
require_once '../../login/api/config.php';

$allowedStatuses = ['todo', 'doing', 'done'];
$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Du lieu gui len khong hop le"
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
        "message" => "Tieu de task khong duoc de trong"
    ]);
    exit;
}

if (!in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Trang thai task khong hop le"
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
        "message" => "Khong tao duoc task"
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Tao task thanh cong",
    "taskId" => mysqli_insert_id($conn)
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
