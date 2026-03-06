class LoginFormComponent extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <div class="login-container">
                <div class="login-content">
                    <h1 class="login-title">Вход в аккаунт</h1>
                    <p class="login-subtitle">Введите свои данные для входа в сервис</p>
                    
                    <form class="login-form" method="POST" action="">
                        <div class="input-icon-wrapper">
                            <input type="email" name="email" class="input-field" placeholder="Введите ваш email" required>
                            <img src="../Images/Icons/EmailIconNormal.png" alt="email icon" class="input-icon">
                        </div>
                        
                        <div class="input-icon-wrapper">
                            <input type="password" name="password" class="input-field" placeholder="Введите пароль" required>
                            <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="show password" class="input-icon">
                        </div>
                        
                        <div class="login-row">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember">
                                <span>Запомнить меня</span>
                            </label>
                            <a href="#" class="forgot-link">Забыли пароль?</a>
                        </div>
                        
                        <button type="submit" class="login-btn">Войти</button>
                    </form>
                    
                    <div class="separator">или</div>
                    
                    <div class="register-text">
                        Нет аккаунта? 
                        <a href="signup.php" class="register-link">Зарегистрируйтесь</a>
                    </div>
                </div>
            </div>
        `;
    }
}

customElements.define('login-form', LoginFormComponent);