<?php
$serverName = "arivell"; // Имя вашего SQL Server
$connectionOptions = [
    "Database" => "nasha_kuhnya",
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true // Доверие к серверному сертификату, если используется SSL
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
