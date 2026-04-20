//Web Component <my-header> — шапка сайта
//подключается ПОСЛЕ utils.js

class HeaderComponent extends HTMLElement {
    connectedCallback() {
        const currentPage = window.location.pathname.split('/').pop();
        const isLoggedIn  = this.getAttribute('logged-in') === 'true';
        const userName    = this.getAttribute('user-name')  || '';
        const userEmail   = this.getAttribute('user-email') || '';

        //определяю активный пункт меню для подсветки
        let activePage = 'home';
        if (currentPage === 'dashboard.php')  activePage = 'dashboard';
        if (currentPage === 'statistics.php') activePage = 'statistics';
        if (currentPage === 'products.php')   activePage = 'products';
        if (currentPage === 'profile.php')    activePage = 'profile';

        let rightContent = '';
        if (isLoggedIn) {
            //авторизован — показываю аватар и дропдаун
            //sanitize защищает от XSS при вставке данных в innerHTML
            const avatarSrc = this.getAttribute('avatar-src') || '../Images/nonAvatar.jpg';
            rightContent = `
                <div class="user-info" id="userInfo">
                    <div class="user-avatar">
                        <img src="${escapeAttr(avatarSrc)}" alt="avatar" id="headerAvatar">
                    </div>
                    <span class="user-name">${sanitize(userName)}</span>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="dropdown-avatar">
                        <img src="${escapeAttr(avatarSrc)}" alt="avatar">
                    </div>
                    <div class="dropdown-name">${sanitize(userName)}</div>
                    <div class="dropdown-email">${sanitize(userEmail)}</div>
                    <div class="dropdown-divider"></div>
                    <a href="profile.php" data-calendar-date-link="profile.php" class="dropdown-item">Настройки профиля</a>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-logout" id="logoutBtn">Выйти</div>
                </div>
            `;
        } else {
            rightContent = `
                <a href="login.php"><button class="btn-login">Войти</button></a>
                <a href="signup.php"><button class="btn-register">Зарегистрироваться</button></a>
            `;
        }

        this.innerHTML = `
            <header class="header">
                <div class="header-content">
                    <div class="header-left">
                        <a href="${isLoggedIn ? 'dashboard.php' : 'landing.php'}" ${isLoggedIn ? 'data-calendar-date-link="dashboard.php"' : ''} class="header-brand">
                            <div class="logo">
                                <img src="../Images/logo.png" alt="FitCalculator logo">
                            </div>
                            <span class="logo-text">FitCalculator</span>
                        </a>
                    </div>
                    <nav class="nav-menu">
                        <a href="landing.php"    class="nav-item ${activePage==='home'       ? 'active':''}">Главная</a>
                        <a href="dashboard.php" data-calendar-date-link="dashboard.php" class="nav-item ${activePage==='dashboard'  ? 'active':''}">Дневник</a>
                        <a href="statistics.php" data-calendar-date-link="statistics.php" class="nav-item ${activePage==='statistics' ? 'active':''}">Статистика</a>
                        <a href="products.php" data-calendar-date-link="products.php" class="nav-item ${activePage==='products'   ? 'active':''}">Продукты</a>
                    </nav>
                    <div class="header-right">${rightContent}</div>
                </div>
            </header>
        `;

        if (isLoggedIn) {
            const userInfo  = this.querySelector('#userInfo');
            const dropdown  = this.querySelector('#dropdownMenu');
            const logoutBtn = this.querySelector('#logoutBtn');

            if (userInfo && dropdown) {
                userInfo.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });
                document.addEventListener('click', (e) => {
                    if (!userInfo.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            }
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    window.location.href = '../Auth/logout.php';
                });
            }
        }
    }
}
customElements.define('my-header', HeaderComponent);
