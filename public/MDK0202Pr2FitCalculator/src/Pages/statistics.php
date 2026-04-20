<?php
require_once __DIR__ . '/../Config/bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../Config/db_connect.php';

$userId = (int)$_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

$period = $_GET['period'] ?? 'day';
if (!in_array($period, ['day', 'week', 'month'], true)) {
    $period = 'day';
}

$selectedDate = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}
$selectedDateEsc = mysqli_real_escape_string($conn, $selectedDate);

$udRes = mysqli_query($conn, "SELECT * FROM userdata WHERE userid = $userId");
$ud = mysqli_fetch_assoc($udRes) ?? [];
$norm = calcDailyCalories($ud);

if ($period === 'day') {
    $sql = "SELECT fd.entry_date,
                   SUM(ROUND(p.calories * fd.serving_weight / 100, 1)) AS cal,
                   SUM(ROUND(p.protein  * fd.serving_weight / 100, 1)) AS prot,
                   SUM(ROUND(p.fat      * fd.serving_weight / 100, 1)) AS fat,
                   SUM(ROUND(p.carbs    * fd.serving_weight / 100, 1)) AS carbs
            FROM fooddiary fd
            JOIN products p ON p.productid = fd.productid
            WHERE fd.userid = $userId
              AND fd.entry_date = '$selectedDateEsc'
            GROUP BY fd.entry_date";
    $summaryLabel = 'Выбранный день';
    $historyTitle = 'Питание за выбранный день';
    $summaryWhere = "fd.userid = $userId AND fd.entry_date = '$selectedDateEsc'";
} elseif ($period === 'week') {
    $sql = "SELECT fd.entry_date,
                   SUM(ROUND(p.calories * fd.serving_weight / 100, 1)) AS cal,
                   SUM(ROUND(p.protein  * fd.serving_weight / 100, 1)) AS prot,
                   SUM(ROUND(p.fat      * fd.serving_weight / 100, 1)) AS fat,
                   SUM(ROUND(p.carbs    * fd.serving_weight / 100, 1)) AS carbs
            FROM fooddiary fd
            JOIN products p ON p.productid = fd.productid
            WHERE fd.userid = $userId
              AND fd.entry_date BETWEEN DATE_SUB('$selectedDateEsc', INTERVAL 6 DAY) AND '$selectedDateEsc'
            GROUP BY fd.entry_date
            ORDER BY fd.entry_date DESC";
    $summaryLabel = 'Последние 7 дней';
    $historyTitle = 'История питания за 7 дней';
    $summaryWhere = "fd.userid = $userId AND fd.entry_date BETWEEN DATE_SUB('$selectedDateEsc', INTERVAL 6 DAY) AND '$selectedDateEsc'";
} else {
    $sql = "SELECT fd.entry_date,
                   SUM(ROUND(p.calories * fd.serving_weight / 100, 1)) AS cal,
                   SUM(ROUND(p.protein  * fd.serving_weight / 100, 1)) AS prot,
                   SUM(ROUND(p.fat      * fd.serving_weight / 100, 1)) AS fat,
                   SUM(ROUND(p.carbs    * fd.serving_weight / 100, 1)) AS carbs
            FROM fooddiary fd
            JOIN products p ON p.productid = fd.productid
            WHERE fd.userid = $userId
              AND fd.entry_date BETWEEN DATE_SUB('$selectedDateEsc', INTERVAL 29 DAY) AND '$selectedDateEsc'
            GROUP BY fd.entry_date
            ORDER BY fd.entry_date DESC";
    $summaryLabel = 'Последние 30 дней';
    $historyTitle = 'История питания за 30 дней';
    $summaryWhere = "fd.userid = $userId AND fd.entry_date BETWEEN DATE_SUB('$selectedDateEsc', INTERVAL 29 DAY) AND '$selectedDateEsc'";
}

$statRes = mysqli_query($conn, $sql);
$statRows = [];
while ($row = mysqli_fetch_assoc($statRes)) {
    $statRows[] = $row;
}

$summaryRes = mysqli_query(
    $conn,
    "SELECT SUM(ROUND(p.calories * fd.serving_weight / 100, 1)) AS cal,
            SUM(ROUND(p.protein  * fd.serving_weight / 100, 1)) AS prot,
            SUM(ROUND(p.fat      * fd.serving_weight / 100, 1)) AS fat,
            SUM(ROUND(p.carbs    * fd.serving_weight / 100, 1)) AS carbs
     FROM fooddiary fd
     JOIN products p ON p.productid = fd.productid
     WHERE $summaryWhere"
);
$summary = mysqli_fetch_assoc($summaryRes) ?: ['cal' => 0, 'prot' => 0, 'fat' => 0, 'carbs' => 0];

$avatarSrc = '../Images/nonAvatar.jpg';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Статистика</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/UI/table.css">
    <link rel="stylesheet" href="../Style/UI/cards.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Layouts/sidebar.css">
    <link rel="stylesheet" href="../Style/Components/calendar.css">
    <link rel="stylesheet" href="../Style/Pages/statistics.css">
    <script src="../Scripts/utils.js"></script>
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
<my-header logged-in="true"
    user-name="<?= e($userName) ?>"
    user-email="<?= e($userEmail) ?>"
    avatar-src="<?= e($avatarSrc) ?>">
</my-header>

<div class="dashboard-layout">
    <?php include '../Config/sidebar.php'; ?>

    <div class="page-with-sidebar">
        <h1></h1>

        <div class="stats-tabs">
            <a href="?period=day&amp;date=<?= urlencode($selectedDate) ?>" class="stats-tab <?= $period === 'day' ? 'active' : '' ?>">День</a>
            <a href="?period=week&amp;date=<?= urlencode($selectedDate) ?>" class="stats-tab <?= $period === 'week' ? 'active' : '' ?>">Неделя</a>
            <a href="?period=month&amp;date=<?= urlencode($selectedDate) ?>" class="stats-tab <?= $period === 'month' ? 'active' : '' ?>">Месяц</a>
        </div>

        <div class="stats-cards-row">
            <div class="card stats-card">
                <p class="stats-card-label"><?= e($summaryLabel) ?></p>
                <div>
                    <span class="stats-card-value"><?= round($summary['cal'] ?? 0) ?></span>
                    <span class="stats-card-unit">ккал</span>
                </div>
                <p class="stats-card-macros">
                    Б: <?= round($summary['prot'] ?? 0) ?>г &nbsp;
                    Ж: <?= round($summary['fat'] ?? 0) ?>г &nbsp;
                    У: <?= round($summary['carbs'] ?? 0) ?>г
                </p>
            </div>
            <div class="card stats-card">
                <p class="stats-card-label">Цель в день</p>
                <div>
                    <span class="stats-card-value"><?= $norm['calories'] ?></span>
                    <span class="stats-card-unit">ккал</span>
                </div>
                <p class="stats-card-macros">
                    Б: <?= $norm['protein'] ?>г &nbsp;
                    Ж: <?= $norm['fat'] ?>г &nbsp;
                    У: <?= $norm['carbs'] ?>г
                </p>
            </div>
        </div>

        <div class="stats-macro-list">
            <div class="stats-macro-item"><div class="macro-dot protein"></div>Белки: <?= round($summary['prot'] ?? 0) ?> г / <?= $norm['protein'] ?> г</div>
            <div class="stats-macro-item"><div class="macro-dot fat"></div>Жиры: <?= round($summary['fat'] ?? 0) ?> г / <?= $norm['fat'] ?> г</div>
            <div class="stats-macro-item"><div class="macro-dot carbs"></div>Углеводы: <?= round($summary['carbs'] ?? 0) ?> г / <?= $norm['carbs'] ?> г</div>
        </div>

        <h2 class="stats-history-title"><?= e($historyTitle) ?></h2>
        <?php if (empty($statRows)): ?>
            <p style="color:var(--text-light);">Записей за этот период нет.</p>
        <?php else: ?>
        <div class="stats-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Калории</th>
                        <th>Белки (г)</th>
                        <th>Жиры (г)</th>
                        <th>Углеводы (г)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statRows as $row): ?>
                    <tr>
                        <td><a href="dashboard.php?date=<?= urlencode($row['entry_date']) ?>" style="color:var(--green);font-weight:500;"><?= formatDateRu($row['entry_date']) ?></a></td>
                        <td><?= round($row['cal']) ?></td>
                        <td><?= round($row['prot']) ?></td>
                        <td><?= round($row['fat']) ?></td>
                        <td><?= round($row['carbs']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<my-footer></my-footer>
<script src="../Scripts/calendar.js"></script>
<script>
const currentPeriod = <?= json_encode($period, JSON_UNESCAPED_UNICODE) ?>;
const cal = new SidebarCalendar(
    'calendarContainer',
    function(dateStr) {
        window.location.href = 'statistics.php?period=' + encodeURIComponent(currentPeriod) + '&date=' + encodeURIComponent(dateStr);
    },
    <?= json_encode($selectedDate, JSON_UNESCAPED_UNICODE) ?>
);

document.getElementById('btnAddMeal')?.addEventListener('click', () => {
    window.location.href = 'dashboard.php?date=' + encodeURIComponent(cal.getSelectedDateStr());
});
</script>
</body>
</html>
