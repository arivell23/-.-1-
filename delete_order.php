<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    // Удаление всех позиций заказа
    $delete_order_items_sql = "DELETE FROM order_items WHERE order_id = ?";
    $params = array($order_id);
    $stmt = sqlsrv_query($conn, $delete_order_items_sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Удаление самого заказа
    $delete_order_sql = "DELETE FROM orders WHERE id = ? AND user_id = ?";
    $params = array($order_id, $user_id);
    $stmt = sqlsrv_query($conn, $delete_order_sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: dashboard.php');
    exit();
} else {
    header('Location: dashboard.php');
    exit();
}
?>
