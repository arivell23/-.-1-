<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Получаем роль из формы или устанавливаем значение по умолчанию
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Отладочное сообщение для проверки значений
    echo "username: $username, email: $email, role: $role, password: $hashed_password";
    // exit(); // Убедитесь, что вы закомментировали или удалили это после тестирования

    $sql = "INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)";
    $params = array($username, $email, $role, $hashed_password);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header('Location: index.html'); // Перенаправление после успешной регистрации
    exit();
}
?>
