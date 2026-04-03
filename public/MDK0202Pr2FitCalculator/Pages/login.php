<?php
session_start();
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Вход</title>
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Components/login-form.css">
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
    <script src="../Components/login-form.js"></script>
    <style>
        main {
        min-height: calc(100vh - 64px - 120px);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 120px 0;
    }

    .message {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }
        
    .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
    .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php
    // Показываем сообщения об ошибках или успехе
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == 'wrong_password') {
            echo '<div class="message message-error">Неверный пароль. Попробуйте снова.</div>';
        } elseif ($error == 'user_not_found') {
            echo '<div class="message message-error">Пользователь с таким email не найден.</div>';
        }
    }
    
    if (isset($_GET['success']) && $_GET['success'] == 'registered') {
        echo '<div class="message message-success">Регистрация успешна! Теперь войдите в аккаунт.</div>';
    }
    ?>
    
    <my-header logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>" 
           user-name="<?php echo htmlspecialchars($userName); ?>"
           user-email="<?php echo htmlspecialchars($userEmail); ?>">
    </my-header>
    
    <main>
        <login-form>

        </login-form>
    </main>
    
    <my-footer></my-footer>
</body>
</html>