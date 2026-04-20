<?php
require_once __DIR__ . '/../Config/bootstrap.php';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name']  ?? '';
$userEmail  = $_SESSION['user_email'] ?? '';
$csrfField  = csrf_field();

//сообщения об ошибках/успехе из GET-параметра
$msgs = [
    'wrong_password'  => ['error',   'Неверный пароль. Попробуйте ещё раз.'],
    'user_not_found'  => ['error',   'Пользователь с таким email не найден.'],
    'empty_fields'    => ['error',   'Заполните все поля.'],
    'registered'      => ['success', 'Регистрация прошла успешно! Теперь войдите.'],
];
$flash = $msgs[$_GET['error'] ?? $_GET['success'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator — Вход</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/UI/inputs.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Components/forms.css">
    <script src="../Scripts/utils.js"></script>
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
    <script src="../Components/login-form.js"></script>
    <style>
        main { display:flex; justify-content:center; align-items:flex-start; padding:5rem 1rem; min-height:calc(100vh - var(--header-h) - 5rem); }
    </style>
</head>
<body>
<?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash[0] ?>"><?= htmlspecialchars($flash[1]) ?></div>
<?php endif; ?>

<my-header
    logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>"
    user-name="<?= htmlspecialchars($userName) ?>"
    user-email="<?= htmlspecialchars($userEmail) ?>">
</my-header>

<main>
    <!-- csrf-field передаю через атрибут в Web Component -->
    <login-form csrf-field="<?= htmlspecialchars($csrfField) ?>"></login-form>
</main>

<my-footer></my-footer>
</body>
</html>
