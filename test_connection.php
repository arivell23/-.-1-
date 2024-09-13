<?php
include 'config.php';

$sql = "SELECT 1";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

echo "Connection successful!";
?>
