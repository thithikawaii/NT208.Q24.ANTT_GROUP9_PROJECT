<?php
require_once '../../login/api/config.php';

try {
    mysqli_query($conn, "SELECT * FROM rollback_demo_table_that_does_not_exist");

    echo json_encode([
        "success" => true,
        "message" => "Trigger khong gay loi"
    ]);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Rollback demo: truy van bang khong ton tai tren staging",
        "error" => $error->getMessage()
    ]);
}

mysqli_close($conn);
?>
