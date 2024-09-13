<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];
    $kitchen_name = $_POST['kitchen_name'];

    $sql = "INSERT INTO orders (user_id, total_price, status, kitchen_name) VALUES (?, ?, ?, ?)";
    $params = array($user_id, $total_price, $status, $kitchen_name);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: admin.php');
    exit();
}
?>
