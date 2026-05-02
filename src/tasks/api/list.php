<?php
require_once '../../login/api/config.php';

$sql = "SELECT id, title, description, status, created_by, created_at, updated_at FROM tasks ORDER BY updated_at DESC, id DESC";
$result = mysqli_query($conn, $sql);

$tasks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tasks[] = [
        "id" => (int) $row['id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "status" => $row['status'],
        "created_by" => $row['created_by'] !== null ? (int) $row['created_by'] : null,
        "created_at" => $row['created_at'],
        "updated_at" => $row['updated_at']
    ];
}

echo json_encode([
    "success" => true,
    "message" => "Lay danh sach task thanh cong",
    "data" => $tasks
]);

mysqli_close($conn);
?>
