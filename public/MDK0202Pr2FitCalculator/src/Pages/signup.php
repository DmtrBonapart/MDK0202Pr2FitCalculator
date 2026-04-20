<?php
require_once __DIR__ . '/../Config/bootstrap.php';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name']  ?? '';
$userEmail  = $_SESSION['user_email'] ?? '';
$csrfField  = csrf_field();

$msgs = [
    'empty_fields'    => ['error', 'Заполните все поля.'],
    'password_short'  => ['error', 'Пароль должен быть не менее 6 символов.'],
    'password_mismatch' => ['error', 'Пароли не совпадают.'],
    'invalid_email'   => ['error', 'Некорректный email.'],
    'email_exists'    => ['error', 'Пользователь с таким email уже существует.'],
    'db_error'        => ['error', 'Ошибка при регистрации. Попробуйте позже.'],
];
$flash = $msgs[$_GET['error'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator — Регистрация</title>
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
    <script src="../Components/signup-form.js"></script>
    <style>
        main { display:flex; justify-content:center; align-items:flex-start; padding:4rem 1rem; min-height:calc(100vh - var(--header-h) - 5rem); }
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
    <signup-form csrf-field="<?= htmlspecialchars($csrfField) ?>"></signup-form>
</main>

<my-footer></my-footer>
</body>
</html>
