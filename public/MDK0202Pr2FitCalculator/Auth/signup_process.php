<?php
// Файл: Auth/signup_process.php
session_start();

require_once '../Config/db_connect.php';

// Получаем данные из формы
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Простая проверка: совпадают ли пароли
if ($password !== $confirm_password) {
    header('Location: ../Pages/signup.php?error=password_mismatch');
    exit;
}

// Проверяем, существует ли пользователь с таким email
$check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

if (mysqli_num_rows($check) > 0) {
    // Пользователь уже существует
    header('Location: ../Pages/signup.php?error=email_exists');
    exit;
}

// Создаем хеш пароля
$hash = password_hash($password, PASSWORD_DEFAULT);

// Добавляем текущую дату
$now = date('Y-m-d H:i:s');

// Создаем нового пользователя
$sql = "INSERT INTO users (user_name, email, password_hash, date_created) VALUES (
    '$name',
    '$email',
    '$hash',
    '$now'
)";

if (mysqli_query($conn, $sql)) {
    // Регистрация успешна - сразу авторизуем пользователя
    $user_id = mysqli_insert_id($conn);
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    
    // Перенаправляем на логин
    header('Location: ../Pages/login.php?success=registered');
    exit;
} else {
    // Ошибка при сохранении
    header('Location: ../Pages/signup.php?error=db_error');
    exit;
}
?>