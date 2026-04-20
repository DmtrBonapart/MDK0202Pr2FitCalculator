<?php
require_once __DIR__ . '/../Config/bootstrap.php';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['user_name'] ?? '';
$avatarSrc  = '../Images/nonAvatar.jpg';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator — Рассчитай свой рацион</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Pages/landing.css">
    <script src="../Scripts/utils.js"></script>
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
<my-header
    logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>"
    user-name="<?= htmlspecialchars($userName) ?>"
    user-email="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>"
    avatar-src="<?= htmlspecialchars($avatarSrc) ?>">
</my-header>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Рассчитай свой рацион за 5 минут</h1>
            <p class="hero-subtitle">Внесите продукты — мы мгновенно посчитаем калории, белки, жиры и углеводы. Просто и удобно.</p>
            <div class="hero-buttons">
                <a href="signup.php"><button class="btn btn-primary btn-large">Начать бесплатно</button></a>
                <a href="login.php"><button class="btn btn-secondary btn-medium">Войти</button></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="../Images/Landing/landing_img1.png" alt="FitCalculator">
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="features-title">Почему выбирают FitCalculator</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><img src="../Images/Icons/bar-chart-2.png" alt=""></div>
                    <h3>Точный расчёт</h3>
                    <p>Автоматический подсчёт КБЖУ с учётом граммовки каждого продукта</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="../Images/Icons/bar-chart-2.png" alt=""></div>
                    <h3>Персональная норма</h3>
                    <p>Норма калорий рассчитывается под ваши параметры и цель по весу</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="../Images/Icons/bar-chart-2.png" alt=""></div>
                    <h3>Удобный дневник</h3>
                    <p>Ведите историю питания по дням и следите за прогрессом</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <h2>Как это работает</h2>
            <p style="text-align:center;color:var(--text-medium);">Всего 3 простых шага</p>
            <div class="steps">
                <div class="step">
                    <div class="step-circle"><span>1</span></div>
                    <h3>Заполните профиль</h3>
                    <p>Укажите параметры и цель</p>
                </div>
                <div class="step">
                    <div class="step-circle"><span>2</span></div>
                    <h3>Добавляйте продукты</h3>
                    <p>Ищите в базе, вводите граммовку</p>
                </div>
                <div class="step">
                    <div class="step-circle"><span>3</span></div>
                    <h3>Следите за прогрессом</h3>
                    <p>Смотрите статистику по дням</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-block">
            <h2 class="cta-title">Готовы начать путь к здоровью?</h2>
            <p class="cta-subtitle">Присоединяйтесь уже сегодня — это бесплатно</p>
            <div class="cta-buttons">
                <a href="signup.php"><button class="btn btn-primary btn-large">Создать аккаунт</button></a>
            </div>
        </div>
    </section>
</main>

<my-footer></my-footer>
</body>
</html>
