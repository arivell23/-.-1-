<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $params = array($username, $email, $role, $hashed_password, $id);
    } else {
        $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $params = array($username, $email, $role, $id);
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: admin.php');
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$params = array($id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать пользователя</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <div class="container">
        <h2>Редактировать пользователя</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label for="role">Роль:</label>
            <select id="role" name="role" required>
                <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>Пользователь</option>
                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Администратор</option>
            </select>
            <label for="password">Пароль (оставьте пустым, если не хотите менять):</label>
            <input type="password" id="password" name="password">
            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>
