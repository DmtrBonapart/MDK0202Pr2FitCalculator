<?php
require_once __DIR__ . '/../Config/bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../Config/db_connect.php';

$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

$udRes = mysqli_query($conn, "SELECT * FROM userdata WHERE userid = $userId");
$ud    = mysqli_fetch_assoc($udRes) ?? [];

$avatarSrc = '../Images/nonAvatar.jpg';
$norm = calcDailyCalories($ud);

$msgs = [
    'saved'        => ['success', 'Изменения сохранены!'],
    'invalid_data' => ['error', 'Проверьте введённые данные.'],
    'db_error'     => ['error', 'Не удалось сохранить данные в базу.'],
];
$flash = $msgs[$_GET['success'] ?? $_GET['error'] ?? ''] ?? null;
$csrfField = csrf_field();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Профиль</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/UI/inputs.css">
    <link rel="stylesheet" href="../Style/UI/cards.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Layouts/sidebar.css">
    <link rel="stylesheet" href="../Style/Components/forms.css">
    <link rel="stylesheet" href="../Style/Components/calendar.css">
    <link rel="stylesheet" href="../Style/Pages/profile.css">
    <script src="../Scripts/utils.js"></script>
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
<?php if ($flash): ?>
    <div class="flash-msg flash-<?= $flash[0] ?>"><?= e($flash[1]) ?></div>
<?php endif; ?>

<my-header logged-in="true"
    user-name="<?= e($userName) ?>"
    user-email="<?= e($userEmail) ?>"
    avatar-src="<?= e($avatarSrc) ?>">
</my-header>

<div class="dashboard-layout">
    <?php include '../Config/sidebar.php'; ?>

    <div class="page-with-sidebar">
        <h1>Настройки профиля</h1>

        <form method="POST" action="../Auth/profile_save.php" class="profile-form">
            <?= $csrfField ?>

            <div class="profile-layout">
                <div>
                    <p class="profile-section-title">Личные данные</p>

                    <div class="form-group">
                        <label class="form-label">Полное имя</label>
                        <div class="input-group" style="margin:0">
                            <input type="text" name="full_name" class="input-field"
                                   value="<?= e($ud['full_name'] ?? $userName) ?>" required>
                            <img src="../Images/Icons/usernormal.png" class="input-icon" alt="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <div class="input-group" style="margin:0">
                            <input type="email" class="input-field"
                                   value="<?= e($userEmail) ?>" disabled
                                   style="background:var(--bg-gray);cursor:not-allowed;">
                            <img src="../Images/Icons/EmailIconNormal.png" class="input-icon" alt="">
                        </div>
                    </div>

                    <p class="profile-section-title">Цели и параметры</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Пол</label>
                            <select name="gender" class="input-field" style="padding-right:2rem;">
                                <option value="male" <?= ($ud['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Мужской</option>
                                <option value="female" <?= ($ud['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Женский</option>
                                <option value="other" <?= ($ud['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Не указывать</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Дата рождения</label>
                            <input type="date" name="birth_date" class="input-field"
                                   value="<?= e($ud['birth_date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Текущий вес (кг)</label>
                            <input type="number" name="current_weight" class="input-field"
                                   placeholder="75" min="30" max="300" step="0.1"
                                   value="<?= e($ud['current_weight'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Желаемый вес (кг)</label>
                            <input type="number" name="target_weight" class="input-field"
                                   placeholder="70" min="30" max="300" step="0.1"
                                   value="<?= e($ud['target_weight'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Рост (см)</label>
                            <input type="number" name="height" class="input-field"
                                   placeholder="175" min="100" max="250" step="0.1"
                                   value="<?= e($ud['height'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Уровень активности</label>
                            <select name="activity_level" class="input-field">
                                <option value="1" <?= ($ud['activity_level'] ?? 1) == 1 ? 'selected' : '' ?>>Сидячий образ жизни</option>
                                <option value="2" <?= ($ud['activity_level'] ?? 1) == 2 ? 'selected' : '' ?>>Лёгкая активность</option>
                                <option value="3" <?= ($ud['activity_level'] ?? 1) == 3 ? 'selected' : '' ?>>Умеренная активность</option>
                                <option value="4" <?= ($ud['activity_level'] ?? 1) == 4 ? 'selected' : '' ?>>Высокая активность</option>
                                <option value="5" <?= ($ud['activity_level'] ?? 1) == 5 ? 'selected' : '' ?>>Очень высокая активность</option>
                            </select>
                        </div>
                    </div>

                    <?php if (!empty($ud)): ?>
                    <div class="card" style="margin-top:0.5rem;margin-bottom:1.5rem;background:var(--green-light);">
                        <p style="font-size:0.875rem;font-weight:600;color:var(--text-dark);margin-bottom:0.25rem;">
                            Ваша расчётная норма
                        </p>
                        <p style="font-size:1rem;color:var(--green);font-weight:700;margin:0;">
                            <?= $norm['calories'] ?> ккал/день
                        </p>
                        <p style="font-size:0.8125rem;color:var(--text-medium);margin:0.25rem 0 0;">
                            Б: <?= $norm['protein'] ?>г &nbsp; Ж: <?= $norm['fat'] ?>г &nbsp; У: <?= $norm['carbs'] ?>г
                        </p>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-medium">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</div>

<my-footer></my-footer>
<script src="../Scripts/calendar.js"></script>
<script>
const cal = new SidebarCalendar('calendarContainer');

document.getElementById('btnAddMeal')?.addEventListener('click', () => {
    window.location.href = 'dashboard.php?date=' + encodeURIComponent(cal.getSelectedDateStr());
});
</script>
</body>
</html>
