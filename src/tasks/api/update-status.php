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

$id = isset($input['id']) ? (int) $input['id'] : 0;
$status = trim($input['status'] ?? '');

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID task khong hop le"
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
    "UPDATE tasks SET status = ? WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) < 0) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Khong doi duoc trang thai task"
    ]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Cap nhat trang thai task thanh cong"
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
