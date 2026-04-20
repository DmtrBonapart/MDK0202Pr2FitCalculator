<?php
session_start();

require_once '../Config/db_connect.php';

// Получаем данные из формы
$email = $_POST['email'];
$password = $_POST['password'];

// Ищем пользователя по email
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // Проверяем пароль
    if (password_verify($password, $user['password_hash'])) {
        // Пароль верный - сохраняем в сессию
        $_SESSION['user_id'] = $user['userid'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_email'] = $user['email'];
        
        // Перенаправляем на дашборд
        header('Location: ../Pages/dashboard.php');
        exit;
    } else {
        // Неверный пароль
        header('Location: ../Pages/login.php?error=wrong_password');
        exit;
    }
} else {
    // Пользователь не найден
    header('Location: ../Pages/login.php?error=user_not_found');
    exit;
}
?>