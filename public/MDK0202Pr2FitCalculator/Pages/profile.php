<?php
session_start();

// Если пользователь не авторизован - отправляем на логин
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Профиль</title>
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
    <my-header logged-in="true" 
    user-name="<?php echo $userName; ?>"
    user-email="<?php echo $_SESSION['user_email']; ?>"
    ></my-header>
    
    <main>
        <div class="container">
            <h1>Профиль</h1>
            <p>Страница профиля</p>
        </div>
    </main>
    
    <my-footer></my-footer>
</body>
</html>