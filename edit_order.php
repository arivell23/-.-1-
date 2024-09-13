<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['id'];
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, array($order_id));
$order = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];
    $kitchen_name = $_POST['kitchen_name'];

    $update_sql = "UPDATE orders SET user_id = ?, total_price = ?, status = ?, kitchen_name = ? WHERE id = ?";
    $params = array($user_id, $total_price, $status, $kitchen_name, $order_id);
    $stmt = sqlsrv_query($conn, $update_sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: admin.php');
    exit();
}

$users_sql = "SELECT id, username FROM users";
$users_stmt = sqlsrv_query($conn, $users_sql);
$users = [];
while ($row = sqlsrv_fetch_array($users_stmt, SQLSRV_FETCH_ASSOC)) {
    $users[] = $row;
}

$products_sql = "SELECT id, name FROM products";
$products_stmt = sqlsrv_query($conn, $products_sql);
$products = [];
while ($row = sqlsrv_fetch_array($products_stmt, SQLSRV_FETCH_ASSOC)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать заказ</title>
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
                    <li><a href="admin.php">Администратор</a></li>
                    <li><a href="logout.php">Выход</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h2>Редактировать заказ</h2>
            <form method="POST" action="edit_order.php?id=<?php echo $order_id; ?>">
                <label for="user_id">Покупатель:</label>
                <select id="user_id" name="user_id" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo $user['id'] == $order['user_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="total_price">Сумма:</label>
                <input type="number" id="total_price" name="total_price" value="<?php echo htmlspecialchars($order['total_price']); ?>" required>

                <label for="status">Статус:</label>
                <select id="status" name="status" required>
                    <option value="new" <?php echo $order['status'] == 'new' ? 'selected' : ''; ?>>Новый</option>
                    <option value="in_progress" <?php echo $order['status'] == 'in_progress' ? 'selected' : ''; ?>>В процессе</option>
                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Завершен</option>
                </select>

                <label for="kitchen_name">Название кухни:</label>
                <select id="kitchen_name" name="kitchen_name" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['name']; ?>" <?php echo $product['name'] == $order['kitchen_name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Сохранить</button>
            </form>
        </div>
    </main>
</body>
</html>
