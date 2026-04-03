class HeaderComponent extends HTMLElement {
    connectedCallback() {
        const currentPage = window.location.pathname.split('/').pop();
        const isLoggedIn = this.getAttribute('logged-in') === 'true';
        const userName = this.getAttribute('user-name') || '';
        const userEmail = this.getAttribute('user-email') || '';
        
        let activePage = 'home';
        if (currentPage === 'dashboard.php') activePage = 'dashboard';
        if (currentPage === 'statistics.php') activePage = 'statistics';
        if (currentPage === 'my-products.php') activePage = 'products';
        if (currentPage === 'landing.php') activePage = 'home';
        
        let rightContent = '';
        if (isLoggedIn) {
            rightContent = `
                <div class="user-info" id="userInfo">
                    <div class="user-avatar">
                        <img src="../Images/nonAvatar.jpg" alt="avatar">
                    </div>
                    <span class="user-name">${userName}</span>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="dropdown-avatar">
                        <img src="../Images/nonAvatar.jpg" alt="avatar">
                    </div>
                    <div class="dropdown-name">${userName}</div>
                    <div class="dropdown-email">${userEmail}</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-logout" id="logoutBtn">Выйти</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-other-profiles">Другие профили</div>
                    <div class="dropdown-add-profile">
                        <img src="../Images/Icons/add_icon_black.png" alt="add" class="add-icon">
                        <span>Добавить профиль</span>
                    </div>
                </div>
            `;
        } else {
            rightContent = `
                <a href="login.php"><button class="btn-login">Войти</button></a>
                <a href="signup.php"><button class="btn-register">Регистрация</button></a>
            `;
        }
        
        this.innerHTML = `
            <header class="header">
                <div class="header-content">
                    <div class="header-left">
                        <a href="landing.php" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                            <div class="logo">
                                <img src="../Images/logo.png" alt="FitCalculator logo">
                            </div>
                            <span class="logo-text">FitCalculator</span>
                        </a>
                    </div>
                    <nav class="nav-menu">
                        <a href="landing.php" class="nav-item ${activePage === 'home' ? 'active' : ''}">Главная</a>
                        <a href="dashboard.php" class="nav-item ${activePage === 'dashboard' ? 'active' : ''}">Дневник</a>
                        <a href="statistics.php" class="nav-item ${activePage === 'statistics' ? 'active' : ''}">Статистика</a>
                        <a href="my-products.php" class="nav-item ${activePage === 'products' ? 'active' : ''}">Продукты</a>
                    </nav>
                    <div class="header-right">
                        ${rightContent}
                    </div>
                </div>
            </header>
        `;
        
        // Добавляем обработчики если пользователь залогинен
        if (isLoggedIn) {
            const userInfo = this.querySelector('#userInfo');
            const dropdown = this.querySelector('#dropdownMenu');
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