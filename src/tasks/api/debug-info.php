<?php
require_once '../../login/api/config.php';

$passwordSource = getenv('DB_PASS') !== false ? 'DB_PASS' : 'DB_PASSWORD';

echo json_encode([
    "success" => true,
    "message" => "Lay thong tin debug thanh cong",
    "data" => [
        "appEnv" => getenv('APP_ENV') ?: 'local-demo',
        "dbHost" => $dbHost,
        "dbName" => $dbName,
        "dbUser" => $dbUser,
        "dbPass" => $dbPass,
        "passwordSource" => $passwordSource
    ]
]);

mysqli_close($conn);
?>
