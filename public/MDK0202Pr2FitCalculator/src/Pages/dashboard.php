<?php
require_once __DIR__ . '/../Config/bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../Config/db_connect.php';

$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

//выбранная дата — из GET-параметра или сегодня
$selectedDate = $_GET['date'] ?? date('Y-m-d');
//защищаю дату от некорректных значений
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
    $selectedDate = date('Y-m-d');
}
$displayDate = formatDateRu($selectedDate);

//получаю физические параметры пользователя для расчёта нормы
$udRes = mysqli_query($conn, "SELECT * FROM userdata WHERE userid = $userId");
$ud    = mysqli_fetch_assoc($udRes) ?? [];
$norm  = calcDailyCalories($ud); //норма КБЖУ на день

//записи дневника за выбранный день с данными продуктов
$date_s  = mysqli_real_escape_string($conn, $selectedDate);
$diaryRes = mysqli_query($conn,
    "SELECT fd.diaryid, fd.serving_weight, p.product_name,
            ROUND(p.calories * fd.serving_weight / 100, 1) AS cal,
            ROUND(p.protein  * fd.serving_weight / 100, 1) AS prot,
            ROUND(p.fat      * fd.serving_weight / 100, 1) AS fat,
            ROUND(p.carbs    * fd.serving_weight / 100, 1) AS carbs
     FROM fooddiary fd
     JOIN products p ON p.productid = fd.productid
     WHERE fd.userid = $userId AND fd.entry_date = '$date_s'
     ORDER BY fd.created_at ASC");

//считаю суммы за день
$totalCal = $totalProt = $totalFat = $totalCarbs = 0;
$diaryRows = [];
while ($row = mysqli_fetch_assoc($diaryRes)) {
    $diaryRows[]  = $row;
    $totalCal    += $row['cal'];
    $totalProt   += $row['prot'];
    $totalFat    += $row['fat'];
    $totalCarbs  += $row['carbs'];
}

//процент выполнения нормы (для прогресс-бара)
$calRatio = $norm['calories'] > 0 ? ($totalCal / $norm['calories']) : 0;
$calPct = $norm['calories'] > 0 ? min(100, round($calRatio * 100)) : 0;
$isCalorieOverLimit = $calRatio >= 1.1;
$currentCaloriesClass = $isCalorieOverLimit ? 'cal-current is-over' : 'cal-current';
$progressFillClass = $isCalorieOverLimit ? 'progress-bar-fill is-over' : 'progress-bar-fill';

//топ-3 популярных продукта пользователя (по количеству добавлений в дневник)
$topRes = mysqli_query($conn,
    "SELECT p.productid, p.product_name, p.calories, p.protein, p.fat, p.carbs
     FROM fooddiary fd
     JOIN products p ON p.productid = fd.productid
     WHERE fd.userid = $userId
     GROUP BY fd.productid
     ORDER BY COUNT(*) DESC
     LIMIT 3");
$topProducts = [];
while ($row = mysqli_fetch_assoc($topRes)) {
    $topProducts[] = $row;
}
//если у пользователя ещё нет истории — беру любые 3 продукта
if (empty($topProducts)) {
    $anyRes = mysqli_query($conn, "SELECT productid, product_name, calories, protein, fat, carbs FROM products LIMIT 3");
    while ($row = mysqli_fetch_assoc($anyRes)) { $topProducts[] = $row; }
}

//аватар
$avatarSrc  = '../Images/nonAvatar.jpg';

$csrfField = csrf_field();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator — Дневник</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/UI/inputs.css">
    <link rel="stylesheet" href="../Style/UI/table.css">
    <link rel="stylesheet" href="../Style/UI/cards.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Layouts/sidebar.css">
    <link rel="stylesheet" href="../Style/Components/modal.css">
    <link rel="stylesheet" href="../Style/Components/calendar.css">
    <link rel="stylesheet" href="../Style/Pages/dashboard.css">
    <script src="../Scripts/utils.js"></script>
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
</head>
<body>
<my-header logged-in="true"
    user-name="<?= htmlspecialchars($userName) ?>"
    user-email="<?= htmlspecialchars($userEmail) ?>"
    avatar-src="<?= htmlspecialchars($avatarSrc) ?>">
</my-header>

<div class="dashboard-layout">
    <?php include '../Config/sidebar.php'; ?>

    <div class="page-with-sidebar">

        <!-- Заголовок с датой -->
        <div class="dashboard-header">
            <h1 class="dashboard-date">Дневник за <?= htmlspecialchars($displayDate) ?></h1>
        </div>

        <!-- Строка: прогресс дня + карусель популярных продуктов -->
        <div class="cards-row">

            <!-- Карточка прогресса КБЖУ -->
            <div class="card progress-card">
                <p class="progress-card-title">Сегодня</p>
                <div class="progress-calories">
                    <span class="<?= $currentCaloriesClass ?>"><?= round($totalCal) ?></span>
                    <span class="cal-goal">/ <?= $norm['calories'] ?> ккал</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="<?= $progressFillClass ?>" style="width:<?= $calPct ?>%"></div>
                </div>
                <div class="macro-row">
                    <span class="macro-item">Б <span><?= round($totalProt) ?>/<?= $norm['protein'] ?>г</span></span>
                    <span class="macro-item">Ж <span><?= round($totalFat)  ?>/<?= $norm['fat']     ?>г</span></span>
                    <span class="macro-item">У <span><?= round($totalCarbs)?>/<?= $norm['carbs']   ?>г</span></span>
                </div>
            </div>

            <!-- Карусель популярных продуктов -->
            <?php if (!empty($topProducts)): ?>
            <div class="carousel-wrap card">
                <p class="carousel-label">Популярные</p>
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($topProducts as $tp): ?>
                    <div class="carousel-slide">
                        <p class="carousel-slide-title"><?= htmlspecialchars($tp['product_name']) ?></p>
                        <p class="carousel-slide-kbju">
                            <?= $tp['calories'] ?> ккал &nbsp;|&nbsp;
                            Б: <?= $tp['protein'] ?>г &nbsp;|&nbsp;
                            Ж: <?= $tp['fat'] ?>г &nbsp;|&nbsp;
                            У: <?= $tp['carbs'] ?>г
                        </p>
                        <button class="btn btn-primary"
                            onclick="openGramModal(<?= $tp['productid'] ?>, '<?= htmlspecialchars(addslashes($tp['product_name'])) ?>', <?= $tp['calories'] ?>, <?= $tp['protein'] ?>, <?= $tp['fat'] ?>, <?= $tp['carbs'] ?>)">
                            + Добавить
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-controls">
                    <button type="button" class="carousel-btn carousel-btn-prev" id="carPrev" aria-label="Предыдущий популярный продукт">&#8592;</button>
                    <button type="button" class="carousel-btn carousel-btn-next" id="carNext" aria-label="Следующий популярный продукт">&#8594;</button>
                </div>
                <div class="carousel-dots" id="carDots">
                    <?php foreach ($topProducts as $i => $_): ?>
                    <div class="carousel-dot <?= $i===0?'active':'' ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Таблица дневника -->
        <div class="diary-table-wrap">
            <div class="diary-table-header">
                <span class="diary-table-title">Записи за <?= htmlspecialchars($displayDate) ?></span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Продукт</th>
                        <th>Вес (г)</th>
                        <th>Ккал</th>
                        <th>Б</th>
                        <th>Ж</th>
                        <th>У</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($diaryRows)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-light);padding:2rem;">
                            Записей за этот день нет. Добавьте первый приём пищи!
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($diaryRows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['product_name']) ?></td>
                        <td><?= $r['serving_weight'] ?></td>
                        <td><?= $r['cal'] ?></td>
                        <td><?= $r['prot'] ?></td>
                        <td><?= $r['fat'] ?></td>
                        <td><?= $r['carbs'] ?></td>
                        <td>
                            <!-- форма удаления записи из дневника -->
                            <form method="POST" action="../Auth/diary_delete.php" style="display:inline">
                                <?= $csrfField ?>
                                <input type="hidden" name="diary_id"   value="<?= $r['diaryid'] ?>">
                                <input type="hidden" name="entry_date" value="<?= htmlspecialchars($selectedDate) ?>">
                                <button type="submit" class="delete-btn" title="Удалить">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Итоговая строка -->
                    <tr>
                        <td><strong>ИТОГО</strong></td>
                        <td>—</td>
                        <td><strong><?= round($totalCal) ?></strong></td>
                        <td><strong><?= round($totalProt) ?></strong></td>
                        <td><strong><?= round($totalFat) ?></strong></td>
                        <td><strong><?= round($totalCarbs) ?></strong></td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /page-with-sidebar -->
</div><!-- /dashboard-layout -->

<my-footer></my-footer>

<!-- Модалка: выбор продукта и граммовки -->
<div class="modal" id="modalAddMeal">
    <div class="modal-overlay"></div>
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Добавить приём пищи</span>
            <button class="modal-close" onclick="closeModal('modalAddMeal')">&times;</button>
        </div>

        <!-- Поиск продукта -->
        <div style="margin-bottom:1rem;">
            <input type="text" id="productSearch" class="input-field" placeholder="Найти продукт...">
        </div>
        <div id="productSearchResults" style="max-height:14rem;overflow-y:auto;margin-bottom:1rem;"></div>

        <!-- Форма добавления в дневник -->
        <form method="POST" action="../Auth/diary_add.php" id="formAddDiary">
            <?= $csrfField ?>
            <input type="hidden" name="entry_date"  value="<?= htmlspecialchars($selectedDate) ?>">
            <input type="hidden" name="product_id"  id="selectedProductId" value="">
            <div id="gramSection" style="display:none;">
                <p class="gram-modal-product" id="gramProductName"></p>
                <p class="gram-modal-kbju"    id="gramProductKbju"></p>
                <div class="input-group">
                    <input type="number" name="serving_weight" id="gramInput" class="input-field"
                           placeholder="Введите количество граммов" min="1" max="9999" step="1">
                </div>
                <div class="gram-preview" id="gramPreview" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddMeal')">Отмена</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitDiary" style="display:none;">Добавить в дневник</button>
            </div>
        </form>
    </div>
</div>

<!-- Модалка граммовки (из карусели) -->
<div class="modal" id="modalGram">
    <div class="modal-overlay"></div>
    <div class="modal-box" style="max-width:26rem;">
        <div class="modal-header">
            <span class="modal-title">Сколько граммов?</span>
            <button class="modal-close" onclick="closeModal('modalGram')">&times;</button>
        </div>
        <form method="POST" action="../Auth/diary_add.php" id="formGram">
            <?= $csrfField ?>
            <input type="hidden" name="entry_date" value="<?= htmlspecialchars($selectedDate) ?>">
            <input type="hidden" name="product_id" id="gramModalProductId">
            <p class="gram-modal-product" id="gramModalName"></p>
            <p class="gram-modal-kbju"    id="gramModalKbju"></p>
            <div class="input-group">
                <input type="number" name="serving_weight" id="gramModalInput" class="input-field"
                       placeholder="Граммов" min="1" max="9999" step="1" required>
            </div>
            <div class="gram-preview" id="gramModalPreview" style="display:none;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalGram')">Отмена</button>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </div>
        </form>
    </div>
</div>

<script src="../Scripts/utils.js"></script>
<script src="../Scripts/modal.js"></script>
<script src="../Scripts/calendar.js"></script>
<script>
//инициализирую календарь с выбранной датой (из URL) и обработчиком выбора дня
const cal = new SidebarCalendar('calendarContainer', function(dateStr) {
    //при клике на день — перехожу на дашборд с нужной датой
    window.location.href = 'dashboard.php?date=' + encodeURIComponent(dateStr);
}, '<?= e($selectedDate) ?>');

//кнопка сайдбара открывает модалку добавления
document.getElementById('btnAddMeal')?.addEventListener('click', () => {
    openModal('modalAddMeal');
});

// ===== Поиск продукта в модалке =====
let searchTimer = null;
const kal100  = {}; //кеш КБЖУ на 100г для выбранного продукта

document.getElementById('productSearch').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('productSearchResults').innerHTML = ''; return; }
    //задержка 300мс чтобы не слать запрос на каждую букву
    searchTimer = setTimeout(() => searchProducts(q), 300);
});

function searchProducts(query) {
    //AJAX-запрос к PHP-скрипту поиска
    fetch('../Auth/product_search.php?q=' + encodeURIComponent(query))
        .then(r => r.json())
        .then(data => {
            const box = document.getElementById('productSearchResults');
            if (!data.length) {
                box.innerHTML = '<p style="color:var(--text-light);padding:0.5rem;">Ничего не найдено</p>';
                return;
            }
            box.innerHTML = data.map(p => `
                <div class="search-result-item" onclick="selectProduct(${p.id}, '${sanitize(p.name)}', ${p.cal}, ${p.prot}, ${p.fat}, ${p.carbs})"
                     style="padding:0.625rem 0.75rem;cursor:pointer;border-radius:var(--radius-md);transition:background 0.15s;">
                    <span style="font-weight:500;">${sanitize(p.name)}</span>
                    <span style="color:var(--text-light);font-size:0.8125rem;margin-left:0.5rem;">${p.cal} ккал/100г</span>
                </div>
            `).join('');
            //подсвечиваю при наведении через JS
            box.querySelectorAll('.search-result-item').forEach(el => {
                el.addEventListener('mouseenter', () => el.style.background = 'var(--bg-gray)');
                el.addEventListener('mouseleave', () => el.style.background = '');
            });
        });
}

//выбор продукта из результатов поиска
function selectProduct(id, name, cal, prot, fat, carbs) {
    document.getElementById('selectedProductId').value = id;
    document.getElementById('gramProductName').textContent = name;
    document.getElementById('gramProductKbju').textContent = cal + ' ккал | Б: ' + prot + 'г | Ж: ' + fat + 'г | У: ' + carbs + 'г';
    kal100[id] = { cal, prot, fat, carbs };
    document.getElementById('gramSection').style.display = 'block';
    document.getElementById('btnSubmitDiary').style.display = 'flex';
    document.getElementById('gramInput').focus();
    document.getElementById('productSearchResults').innerHTML = '';
    document.getElementById('productSearch').value = name;
}

//обновляю превью КБЖУ при вводе граммовки
document.getElementById('gramInput')?.addEventListener('input', function() {
    const id  = document.getElementById('selectedProductId').value;
    const g   = parseFloat(this.value);
    const pre = document.getElementById('gramPreview');
    if (!id || !kal100[id] || isNaN(g) || g <= 0) { pre.style.display='none'; return; }
    const k = kal100[id];
    pre.style.display = 'block';
    pre.textContent = 'За ' + g + ' г: ' +
        Math.round(k.cal * g/100) + ' ккал, Б ' + Math.round(k.prot*g/100*10)/10 +
        'г, Ж ' + Math.round(k.fat*g/100*10)/10 + 'г, У ' + Math.round(k.carbs*g/100*10)/10 + 'г';
});

// ===== Карусель =====
let carIdx = 0;
const slides = <?= count($topProducts) ?>;
function updateCarousel() {
    document.getElementById('carouselTrack').style.transform = `translateX(-${carIdx*100}%)`;
    document.querySelectorAll('.carousel-dot').forEach((d,i) => {
        d.classList.toggle('active', i === carIdx);
    });
}
document.getElementById('carNext')?.addEventListener('click', () => { carIdx = (carIdx+1) % slides; updateCarousel(); });
document.getElementById('carPrev')?.addEventListener('click', () => { carIdx = (carIdx-1+slides) % slides; updateCarousel(); });

//открытие модалки граммовки из карусели
function openGramModal(id, name, cal, prot, fat, carbs) {
    document.getElementById('gramModalProductId').value = id;
    document.getElementById('gramModalName').textContent = name;
    document.getElementById('gramModalKbju').textContent = cal + ' ккал | Б: ' + prot + 'г | Ж: ' + fat + 'г | У: ' + carbs + 'г';
    kal100[id] = { cal, prot, fat, carbs };
    document.getElementById('gramModalPreview').style.display = 'none';
    openModal('modalGram');
    document.getElementById('gramModalInput').value = '';
    document.getElementById('gramModalInput').focus();
}

document.getElementById('gramModalInput')?.addEventListener('input', function() {
    const id  = document.getElementById('gramModalProductId').value;
    const g   = parseFloat(this.value);
    const pre = document.getElementById('gramModalPreview');
    if (!id || !kal100[id] || isNaN(g) || g <= 0) { pre.style.display='none'; return; }
    const k = kal100[id];
    pre.style.display = 'block';
    pre.textContent = 'За ' + g + ' г: ' +
        Math.round(k.cal*g/100) + ' ккал, Б ' + Math.round(k.prot*g/100*10)/10 +
        'г, Ж ' + Math.round(k.fat*g/100*10)/10 + 'г, У ' + Math.round(k.carbs*g/100*10)/10 + 'г';
});
</script>
</body>
</html>
