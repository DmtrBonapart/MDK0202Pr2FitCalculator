<?php
require_once __DIR__ . '/../Config/bootstrap.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../Config/db_connect.php';

$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'name';
$sortMap = ['name' => 'product_name', 'cal' => 'calories', 'prot' => 'protein', 'fat' => 'fat', 'carbs' => 'carbs'];
$orderCol = $sortMap[$sort] ?? 'product_name';

$perPage = 12;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$where = '1=1';
if ($filter === 'mine') { $where = "created_by_userid = $userId"; }

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND product_name LIKE '%$s%'";
}

$cntRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products WHERE $where");
$cntRow = mysqli_fetch_assoc($cntRes);
$total  = (int)$cntRow['cnt'];
$pages  = max(1, (int)ceil($total / $perPage));

$prodRes = mysqli_query(
    $conn,
    "SELECT p.*, u.user_name AS added_by
     FROM products p
     LEFT JOIN users u ON u.userid = p.created_by_userid
     WHERE $where
     ORDER BY (p.created_by_userid = $userId) DESC, $orderCol ASC
     LIMIT $perPage OFFSET $offset"
);

$avatarSrc = '../Images/nonAvatar.jpg';
$csrfField = csrf_field();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Продукты</title>
    <link rel="icon" type="image/png" href="../Images/logo.png">
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/UI/inputs.css">
    <link rel="stylesheet" href="../Style/UI/cards.css">
    <link rel="stylesheet" href="../Style/UI/pagination.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Layouts/sidebar.css">
    <link rel="stylesheet" href="../Style/Components/modal.css">
    <link rel="stylesheet" href="../Style/Components/calendar.css">
    <link rel="stylesheet" href="../Style/Pages/products.css">
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
        <div class="products-header">
            <h1></h1>
            <button class="btn btn-primary" onclick="openModal('modalAddProduct')">+ Новый продукт</button>
        </div>

        <form method="GET" action="" class="products-filters">
            <div class="search-wrap">
                <input type="text" name="search" class="search-input" placeholder="Поиск продуктов..."
                       value="<?= e($search) ?>">
            </div>
            <a href="?filter=all&sort=<?= e($sort) ?>" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">Все продукты</a>
            <a href="?filter=mine&sort=<?= e($sort) ?>" class="filter-btn <?= $filter === 'mine' ? 'active' : '' ?>">Мои</a>
            <a href="?filter=<?= e($filter) ?>&sort=cal" class="filter-btn <?= $sort === 'cal' ? 'active' : '' ?>">По калориям</a>
            <a href="?filter=<?= e($filter) ?>&sort=prot" class="filter-btn <?= $sort === 'prot' ? 'active' : '' ?>">По белкам</a>
            <a href="?filter=<?= e($filter) ?>&sort=fat" class="filter-btn <?= $sort === 'fat' ? 'active' : '' ?>">По жирам</a>
            <a href="?filter=<?= e($filter) ?>&sort=carbs" class="filter-btn <?= $sort === 'carbs' ? 'active' : '' ?>">По углеводам</a>
            <button type="submit" class="btn btn-ghost" style="height:2.5rem;padding:0 1rem;">Найти</button>
        </form>

        <div class="products-grid">
        <?php while ($p = mysqli_fetch_assoc($prodRes)): ?>
            <div class="card product-card">
                <img class="product-card-img" src="../Images/foodimg.jpg" alt="<?= e($p['product_name']) ?>">
                <div class="product-card-body">
                    <p class="product-card-name"><?= e($p['product_name']) ?></p>
                    <div class="product-card-kbju">
                        <span class="kbju-badge kal"><?= $p['calories'] ?> ккал</span>
                        <span class="kbju-badge">Б <?= $p['protein'] ?>г</span>
                        <span class="kbju-badge">Ж <?= $p['fat'] ?>г</span>
                        <span class="kbju-badge">У <?= $p['carbs'] ?>г</span>
                    </div>
                    <p class="product-card-author">Добавил: <?= e($p['added_by'] ?? 'система') ?></p>
                    <div class="product-card-actions">
                        <button class="btn btn-primary"
                            onclick="openGramModal(<?= $p['productid'] ?>, '<?= e(addslashes($p['product_name'])) ?>', <?= $p['calories'] ?>, <?= $p['protein'] ?>, <?= $p['fat'] ?>, <?= $p['carbs'] ?>)">
                            + В дневник
                        </button>
                        <?php if ((int)$p['created_by_userid'] === $userId): ?>
                        <form method="POST" action="../Auth/product_delete.php" style="display:inline">
                            <?= $csrfField ?>
                            <input type="hidden" name="product_id" value="<?= $p['productid'] ?>">
                            <button type="submit" class="btn btn-ghost btn-trash" title="Удалить"
                                    onclick="return confirm('Удалить этот продукт?')">
                                <img src="../Images/Icons/trashicon.png" alt="" width="16" height="16">
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?filter=<?= e($filter) ?>&sort=<?= e($sort) ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"
               class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<my-footer></my-footer>

<div class="modal" id="modalAddProduct">
    <div class="modal-overlay"></div>
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Добавить новый продукт</span>
            <button class="modal-close" onclick="closeModal('modalAddProduct')">&times;</button>
        </div>
        <form method="POST" action="../Auth/product_add.php">
            <?= $csrfField ?>
            <div class="input-group">
                <input type="text" name="product_name" class="input-field" placeholder="Название продукта" required>
            </div>
            <p style="font-size:0.875rem;color:var(--text-medium);margin-bottom:0.75rem;">КБЖУ на 100 г:</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0;">
                <div class="input-group">
                    <input type="number" name="calories" class="input-field" placeholder="Калории" min="0" step="0.1" required>
                </div>
                <div class="input-group">
                    <input type="number" name="protein" class="input-field" placeholder="Белки (г)" min="0" step="0.1" required>
                </div>
                <div class="input-group">
                    <input type="number" name="fat" class="input-field" placeholder="Жиры (г)" min="0" step="0.1" required>
                </div>
                <div class="input-group">
                    <input type="number" name="carbs" class="input-field" placeholder="Углеводы (г)" min="0" step="0.1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddProduct')">Отмена</button>
                <button type="submit" class="btn btn-primary">Сохранить продукт</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="modalGram">
    <div class="modal-overlay"></div>
    <div class="modal-box" style="max-width:26rem;">
        <div class="modal-header">
            <span class="modal-title">Добавить в дневник</span>
            <button class="modal-close" onclick="closeModal('modalGram')">&times;</button>
        </div>
        <form method="POST" action="../Auth/diary_add.php" id="formGram">
            <?= $csrfField ?>
            <input type="hidden" name="entry_date" value="<?= e(date('Y-m-d')) ?>">
            <input type="hidden" name="product_id" id="gramProdId">
            <p class="gram-modal-product" id="gramProdName"></p>
            <p class="gram-modal-kbju" id="gramProdKbju"></p>
            <div class="input-group">
                <input type="number" name="serving_weight" id="gramWeight" class="input-field"
                       placeholder="Граммов" min="1" max="9999" step="1" required>
            </div>
            <div class="gram-preview" id="gramProdPreview" style="display:none;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalGram')">Отмена</button>
                <button type="submit" class="btn btn-primary">Добавить</button>
            </div>
        </form>
    </div>
</div>

<script src="../Scripts/modal.js"></script>
<script src="../Scripts/calendar.js"></script>
<script>
const cal = new SidebarCalendar('calendarContainer');

document.getElementById('btnAddMeal')?.addEventListener('click', () => {
    window.location.href = 'dashboard.php?date=' + encodeURIComponent(cal.getSelectedDateStr());
});

const gramKbju = {};
function openGramModal(id, name, cal, prot, fat, carbs) {
    gramKbju[id] = {cal, prot, fat, carbs};
    document.getElementById('gramProdId').value = id;
    document.getElementById('gramProdName').textContent = name;
    document.getElementById('gramProdKbju').textContent = cal + ' ккал | Б: ' + prot + 'г | Ж: ' + fat + 'г | У: ' + carbs + 'г';
    document.getElementById('gramProdPreview').style.display = 'none';
    document.getElementById('gramWeight').value = '';
    openModal('modalGram');
    document.getElementById('gramWeight').focus();
}

document.getElementById('gramWeight')?.addEventListener('input', function() {
    const id = document.getElementById('gramProdId').value;
    const g = parseFloat(this.value);
    const pre = document.getElementById('gramProdPreview');
    if (!gramKbju[id] || isNaN(g) || g <= 0) { pre.style.display = 'none'; return; }
    const k = gramKbju[id];
    pre.style.display = 'block';
    pre.textContent = 'За ' + g + ' г: ' + Math.round(k.cal * g / 100) + ' ккал, Б ' +
        Math.round(k.prot * g / 100 * 10) / 10 + 'г, Ж ' + Math.round(k.fat * g / 100 * 10) / 10 +
        'г, У ' + Math.round(k.carbs * g / 100 * 10) / 10 + 'г';
});
</script>
</body>
</html>
