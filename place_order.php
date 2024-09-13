<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Получение товаров из корзины для текущего пользователя
    $sql = "SELECT cart.*, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?";
    $params = array($user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $cart_items = [];
    $total_price = 0;

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $cart_items[] = $row;
        $total_price += $row['total_price'];
    }

    // Если корзина пуста, перенаправляем на страницу корзины
    if (empty($cart_items)) {
        header('Location: dashboard.php#cart');
        exit();
    }

    // Создание заказа
    $sql = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)";
    $params = array($user_id, $total_price, 'Новый');
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Получение ID созданного заказа
    $sql = "SELECT SCOPE_IDENTITY() AS id";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $order_id = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['id'];

    // Добавление товаров в таблицу order_items
    foreach ($cart_items as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $params = array($order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    // Очистка корзины
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $params = array($user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Перенаправление на страницу заказов
    header('Location: dashboard.php#orders');
    exit();
}
?>
