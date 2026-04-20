<?php
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

csrf_verify();

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../Pages/login.php?error=empty_fields');
    exit;
}

$email_safe = mysqli_real_escape_string($conn, $email);
$result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_safe'");

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['userid'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_email'] = $user['email'];

        header('Location: ../Pages/dashboard.php');
        exit;
    }

    header('Location: ../Pages/login.php?error=wrong_password');
    exit;
}

header('Location: ../Pages/login.php?error=user_not_found');
exit;
