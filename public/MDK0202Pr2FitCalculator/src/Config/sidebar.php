<?php
//общий сайдбар для авторизованных страниц
//подключается через include на каждой странице после session_start

//определяю текущую страницу для подсветки
$currentPage = basename($_SERVER['PHP_SELF']);
$isActive = function(string $page) use ($currentPage): string {
    return $currentPage === $page ? 'active' : '';
};
?>
<aside class="sidebar">
    <p class="sidebar-title">Мой дневник</p>

    <nav class="sidebar-nav">
        <a href="dashboard.php" data-calendar-date-link="dashboard.php" class="sidebar-link <?= $isActive('dashboard.php') ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Дневник
        </a>
        <a href="statistics.php" data-calendar-date-link="statistics.php" class="sidebar-link <?= $isActive('statistics.php') ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6"  y1="20" x2="6"  y2="14"/></svg>
            Статистика
        </a>
        <a href="products.php" data-calendar-date-link="products.php" class="sidebar-link <?= $isActive('products.php') ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Мои продукты
        </a>
        <a href="profile.php" data-calendar-date-link="profile.php" class="sidebar-link <?= $isActive('profile.php') ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Настройки
        </a>
    </nav>

    <div class="quick-actions">
        <p class="quick-actions-title">Быстрые действия</p>
        <button class="btn-add-meal" id="btnAddMeal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Добавить приём пищи
        </button>
    </div>

    <div class="sidebar-section">
        <p class="sidebar-section-title">Календарь</p>
        <div id="calendarContainer"></div>
    </div>
</aside>
