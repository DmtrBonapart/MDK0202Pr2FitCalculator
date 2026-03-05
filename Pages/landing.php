<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Главная</title>
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Landing/hero.css">
    <link rel="stylesheet" href="../Style/Landing/features.css">
    <link rel="stylesheet" href="../Style/Landing/how-it-works.css">
    <link rel="stylesheet" href="../Style/Landing/cta.css">
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
    <my-header></my-header>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Рассчитай свой рацион за 5 минут</h1>
                <p class="hero-subtitle">Внесите свои продукты, и мы мгновенно посчитаем калории, белки, жиры и углеводы. Просто и удобно.</p>
                <div class="hero-buttons">
                    <button class="hero-btn-primary">Начать расчет</button>
                    <button class="hero-btn-secondary">Узнать больше</button>
                </div>
            </div>
            <div class="hero-image">
                <img src="../Images/Landing/landing_img1.png" alt="FitCalculator app illustration" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
            </div>
        </section>

        <section class="features">
    <h2 class="features-title">Почему выбирают FitCalculator</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-header">
                <div class="feature-icon">
                    <img src="../Images/Icons/bar-chart-2.png" alt="icon">
                </div>
                <h3>Точный расчет</h3>
            </div>
            <p>Автоматический подсчет КБЖУ на основе базы продуктов.</p>
        </div>
        <div class="feature-card">
            <div class="feature-header">
                <div class="feature-icon">
                    <img src="../Images/Icons/bar-chart-2.png" alt="icon">
                </div>
                <h3>Большая база</h3>
            </div>
            <p>Более 5000 продуктов и блюд с проверенными данными.</p>
        </div>
        <div class="feature-card">
            <div class="feature-header">
                <div class="feature-icon">
                    <img src="../Images/Icons/bar-chart-2.png" alt="icon">
                </div>
                <h3>Удобный дневник</h3>
            </div>
            <p>Ведите историю питания и следите за прогрессом.</p>
        </div>
    </div>
</section>

        <section class="how-it-works">
            <h2 class="how-it-works-title">Как это работает</h2>
            <p class="how-it-works-subtitle">Всего 3 простых шага для достижения цели</p>
    
            <div class="steps">
                <div class="step">
                    <div class="step-circle"><span>1</span></div>
                    <h3>Заполните профиль</h3>
                    <p>Укажите параметры и цели</p>
                </div>
        
                <div class="step">
                    <div class="step-circle"><span>2</span></div>
                    <h3>Добавляйте продукты</h3>
                    <p>Ищите в базе или создавайте свои</p>
                </div>
        
                <div class="step">
                    <div class="step-circle"><span>3</span></div>
                    <h3>Следите за прогрессом</h3>
                    <p>Анализируйте статистику и результаты</p>
                </div>
            </div>
        </section>
        
        <section class="cta">
            <div class="cta-block">
                <h2 class="cta-title">Готовы начать свой путь к здоровью?</h2>
                <p class="cta-subtitle">Присоединяйтесь к тысячам пользователей уже сегодня</p>
                <div class="cta-buttons">
                    <a href="signup.php"><button class="cta-btn-primary">Создать аккаунт бесплатно</button></a>
                    <button class="cta-btn-secondary">Узнать больше</button>
                </div>
            </div>
        </section>

    </main>
    
    <my-footer></my-footer>
</body>
</html>