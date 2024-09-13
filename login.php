<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Для отладки выводим введенные данные
    echo "Введенное имя пользователя: $username<br>";
    echo "Введенный пароль: $password<br>";

    // Получение пользователя из базы данных по имени пользователя
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = sqlsrv_query($conn, $sql, array($username));

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        echo "Найденный пользователь:<br>";
        print_r($user);

        // Проверка пароля
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            echo "<br>Пароль не совпадает<br>";
        }
    } else {
        echo "<br>Пользователь не найден<br>";
    }
    echo "Неверные учетные данные";
}
?>

