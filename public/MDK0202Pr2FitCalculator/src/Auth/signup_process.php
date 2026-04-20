<?php
//обработчик формы регистрации
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

csrf_verify();

$name             = trim($_POST['name']             ?? '');
$email            = trim($_POST['email']            ?? '');
$password         =      $_POST['password']         ?? '';
$confirm_password =      $_POST['confirm_password'] ?? '';

//валидация входных данных
if (empty($name) || empty($email) || empty($password)) {
    header('Location: ../Pages/signup.php?error=empty_fields');
    exit;
}

//проверяю минимальную длину пароля — 6 символов
if (strlen($password) < 6) {
    header('Location: ../Pages/signup.php?error=password_short');
    exit;
}

if ($password !== $confirm_password) {
    header('Location: ../Pages/signup.php?error=password_mismatch');
    exit;
}

//проверяю формат email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../Pages/signup.php?error=invalid_email');
    exit;
}

$email_safe = mysqli_real_escape_string($conn, $email);
$check = mysqli_query($conn, "SELECT userid FROM users WHERE email = '$email_safe'");
if (mysqli_num_rows($check) > 0) {
    header('Location: ../Pages/signup.php?error=email_exists');
    exit;
}

//хеширую пароль через bcrypt
$hash     = password_hash($password, PASSWORD_DEFAULT);
$name_s   = mysqli_real_escape_string($conn, $name);
$now      = date('Y-m-d H:i:s');

$sql = "INSERT INTO users (user_name, email, password_hash, date_created)
        VALUES ('$name_s', '$email_safe', '$hash', '$now')";

if (mysqli_query($conn, $sql)) {
    $user_id = mysqli_insert_id($conn);

    //сразу авторизую пользователя
    $_SESSION['user_id']    = $user_id;
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;

    header('Location: ../Pages/login.php?success=registered');
    exit;
} else {
    header('Location: ../Pages/signup.php?error=db_error');
    exit;
}
