<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $user_id = $_SESSION['user_id'];

    $delete_cart_item_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $params = array($cart_id, $user_id);
    $stmt = sqlsrv_query($conn, $delete_cart_item_sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: dashboard.php');
    exit();
}
?>
