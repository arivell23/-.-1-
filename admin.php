<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$stmt_users = sqlsrv_query($conn, 'SELECT * FROM users');
$stmt_orders = sqlsrv_query($conn, 'SELECT * FROM orders');

if ($stmt_users === false || $stmt_orders === false) {
    die(print_r(sqlsrv_errors(), true));
}

$users = [];
while ($row = sqlsrv_fetch_array($stmt_users, SQLSRV_FETCH_ASSOC)) {
    $users[] = $row;
}

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
    <title>Панель администратора</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="images/logo.jpg" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Главная</a></li>
                    <li><a href="admin.php">Администратор</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Добро пожаловать, Администратор!</h2>
            <p>Здесь вы можете управлять данными о пользователях и заказах.</p>

            <section class="admin-section">
                <h3>Управление пользователями</h3>
                <button id="addUserBtn">Добавить пользователя</button>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя пользователя</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Дата регистрации</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']->format('Y-m-d H:i:s')); ?></td>
                                <td><a href="edit_user.php?id=<?php echo $user['id']; ?>">Редактировать</a></td>
                                <td><a href="delete_user.php?id=<?php echo $user['id']; ?>">Удалить</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="admin-section">
                <h3>Управление заказами</h3>
                <button id="addOrderBtn">Добавить заказ</button>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Пользователя</th>
                            <th>Название кухни</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['kitchen_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td><?php echo htmlspecialchars($order['created_at']->format('Y-m-d H:i:s')); ?></td>
                                <td><a href="edit_order.php?id=<?php echo $order['id']; ?>">Редактировать</a></td>
                                <td><a href="delete_order.php?id=<?php echo $order['id']; ?>">Удалить</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Компания по изготовлению кухонь. Все права защищены.</p>
        </div>
    </footer>

    <!-- Модальные окна для добавления пользователей и заказов -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('userModal')">&times;</span>
            <h2>Добавить покупателя</h2>
            <form id="userForm" method="POST" action="add_user.php">
                <label for="username">Имя покупателя:</label>
                <input type="text" id="username" name="username" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="role">Роль:</label>
                <select id="role" name="role" required>
                    <option value="user">Покупатель</option>
                    <option value="admin">Администратор</option>
                </select>
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('orderModal')">&times;</span>
            <h2>Добавить заказ</h2>
            <form id="orderForm" method="POST" action="add_order.php">
                <label for="user_id">Покупатель:</label>
                <select id="user_id" name="user_id" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="total_price">Сумма:</label>
                <input type="number" id="total_price" name="total_price" required>
                <label for="status">Статус:</label>
                <select id="status" name="status" required>
                    <option value="new">Новый</option>
                    <option value="in_progress">В процессе</option>
                    <option value="completed">Завершен</option>
                </select>
                <label for="kitchen_name">Название кухни:</label>
                <input type="text" id="kitchen_name" name="kitchen_name" required>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>

    <script src="js/admin-scripts.js"></script>
</body>
</html>
