class HeaderComponent extends HTMLElement {
    connectedCallback() {
        const currentPage = window.location.pathname.split('/').pop();
        
        let activePage = 'home';
        if (currentPage === 'dashboard.php') activePage = 'dashboard';
        if (currentPage === 'statistics.php') activePage = 'statistics';
        if (currentPage === 'my-products.php') activePage = 'products';
        if (currentPage === 'landing.php') activePage = 'home';
        
        this.innerHTML = `
            <header class="header">
                <div class="header-content">
                    <div class="header-left">
                        <a href="landing.php" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                            <div class="logo">
                                <img src="../Images/logo.png" alt="FitCalculator logo" style="width: 100%; height: 100%; object-fit: contain;">
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
                        <a href="login.php"><button class="btn-login">Войти</button></a>
                        <a href="signup.php"><button class="btn-register">Регистрация</button></a>
                    </div>
                </div>
            </header>
        `;
    }
}

customElements.define('my-header', HeaderComponent);