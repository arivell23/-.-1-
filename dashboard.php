<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Подключение к базе данных
include 'config.php';

// Получение данных для каталога, корзины и заказов
$user_id = $_SESSION['user_id'];

// Получение продуктов для каталога
$stmt_products = sqlsrv_query($conn, 'SELECT * FROM products');
$products = [];
while ($row = sqlsrv_fetch_array($stmt_products, SQLSRV_FETCH_ASSOC)) {
    $products[] = $row;
}

// Получение товаров в корзине для текущего пользователя
$stmt_cart = sqlsrv_query($conn, 'SELECT cart.*, products.name, products.image FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?', array($user_id));
$cart_items = [];
while ($row = sqlsrv_fetch_array($stmt_cart, SQLSRV_FETCH_ASSOC)) {
    $cart_items[] = $row;
}

// Получение истории заказов для текущего пользователя
$stmt_orders = sqlsrv_query($conn, 'SELECT * FROM orders WHERE user_id = ?', array($user_id));
$orders = [];
while ($row = sqlsrv_fetch_array($stmt_orders, SQLSRV_FETCH_ASSOC)) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cart_styles.css"> <!-- Подключение нового CSS файла -->
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="images/logo.jpg" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="#catalog">Каталог</a></li>
                    <li><a href="#cart">Корзина</a></li>
                    <li><a href="#orders">Мои заказы</a></li>
                    <li><a href="logout.php">Выход</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="catalog">
            <div class="container">
                <h2>Каталог</h2>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><?php echo htmlspecialchars($product['price']); ?> руб.</p>
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="10">
                                <button type="submit">Добавить в корзину</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="cart">
            <div class="container">
                <h2>Корзина</h2>
                <div class="cart-items">
                    <?php if (empty($cart_items)): ?>
                        <p>Ваша корзина пуста.</p>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p><?php echo htmlspecialchars($item['quantity']); ?> шт.</p>
                                <p><?php echo htmlspecialchars($item['total_price']); ?> руб.</p>
                                <form action="remove_from_cart.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit">Удалить</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        <div class="order-section">
                            <form action="place_order.php" method="POST">
                                <button type="submit">Оформить заказ</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="orders">
            <div class="container">
                <h2>Мои заказы</h2>
                <div class="order-history">
                    <?php if (empty($orders)): ?>
                        <p>У вас нет заказов.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-item">
                                <h3>Заказ №<?php echo htmlspecialchars($order['id']); ?></h3>
                                <p>Дата: <?php echo htmlspecialchars($order['created_at']->format('Y-m-d H:i:s')); ?></p>
                                <p>Сумма: <?php echo htmlspecialchars($order['total_price']); ?> руб.</p>
                                <p>Статус: <?php echo htmlspecialchars($order['status']); ?></p>
                                <form action="delete_order.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit">Удалить</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Компания по изготовлению кухонь. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>
