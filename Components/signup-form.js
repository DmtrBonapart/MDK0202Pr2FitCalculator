class SignupFormComponent extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <div class="signup-container">
                <div class="signup-content">
                    <h1 class="signup-title">Создать аккаунт</h1>
                    <p class="signup-subtitle">Заполните форму для регистрации</p>
                    
                    <form class="signup-form" method="POST" action="">
                        <div class="input-icon-wrapper">
                            <input type="text" name="name" class="input-field" placeholder="Введите ваше имя" required>
                            <img src="../Images/Icons/usernormal.png" alt="user icon" class="input-icon">
                        </div>
                        
                        <div class="input-icon-wrapper">
                            <input type="email" name="email" class="input-field" placeholder="Введите ваш email" required>
                            <img src="../Images/Icons/EmailIconNormal.png" alt="email icon" class="input-icon">
                        </div>
                        
                        <div class="input-icon-wrapper">
                            <input type="password" name="password" class="input-field" placeholder="Введите пароль" required>
                            <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="password icon" class="input-icon">
                        </div>
                        
                        <div class="input-icon-wrapper">
                            <input type="password" name="confirm_password" class="input-field" placeholder="Подтвердите пароль" required>
                            <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="confirm password icon" class="input-icon">
                        </div>
                        
                        <button type="submit" class="signup-btn">Зарегистрироваться</button>
                    </form>
                    
                    <div class="login-link-text">
                        Уже есть аккаунт?
                        <a href="login.php">Войти</a>
                    </div>
                </div>
            </div>
        `;
    }
}

customElements.define('signup-form', SignupFormComponent);