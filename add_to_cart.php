<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Получаем цену продукта
    $sql = "SELECT price FROM products WHERE id = ?";
    $params = array($product_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $price = $row['price'];

    // Рассчитываем общую цену
    $total_price = $price * $quantity;

    // Добавляем товар в корзину
    $sql = "INSERT INTO cart (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)";
    $params = array($user_id, $product_id, $quantity, $total_price);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: dashboard.php#cart');
    exit();
}
?>
