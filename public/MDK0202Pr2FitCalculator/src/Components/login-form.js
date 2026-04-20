//Web Component <login-form> — форма входа
class LoginFormComponent extends HTMLElement {
    connectedCallback() {
        const csrf = this.getAttribute('csrf-field') || '';
        this.innerHTML = `
            <div class="auth-card">
                <h1 class="auth-title">Вход в аккаунт</h1>
                <p class="auth-subtitle">Введите свои данные для входа</p>
                <form class="auth-form" method="POST" action="../Auth/login_process.php">
                    ${csrf}
                    <div class="input-group">
                        <input type="email" name="email" class="input-field"
                               placeholder="Введите ваш email" required autocomplete="email">
                        <img src="../Images/Icons/EmailIconNormal.png" class="input-icon" alt="">
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" class="input-field"
                               placeholder="Введите пароль" required autocomplete="current-password">
                        <button type="button" class="input-icon-btn" data-toggle="password" aria-label="Показать пароль">
                            <span class="eye-wrap is-hidden" style="display:flex;align-items:center;justify-content:center;">
                                <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="" class="eye-icon">
                            </span>
                        </button>
                    </div>
                    <div class="auth-row auth-row-end">
                        <a href="#" class="link-blue">Забыли пароль?</a>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-full">Войти</button>
                </form>
                <div class="auth-footer-text">
                    Нет аккаунта? <a href="signup.php" class="link-green">Зарегистрируйтесь</a>
                </div>
            </div>
        `;

        const passInput = this.querySelector('input[name="password"]');
        const toggleBtn = this.querySelector('[data-toggle="password"]');
        const eyeWrap = toggleBtn?.querySelector('.eye-wrap');
        if (passInput && toggleBtn && eyeWrap) {
            toggleBtn.addEventListener('click', () => {
                const isHidden = passInput.type === 'password';
                if (isHidden) {
                    passInput.type = 'text';
                    eyeWrap.classList.remove('is-hidden');
                    toggleBtn.setAttribute('aria-label', 'Скрыть пароль');
                } else {
                    passInput.type = 'password';
                    eyeWrap.classList.add('is-hidden');
                    toggleBtn.setAttribute('aria-label', 'Показать пароль');
                }
                passInput.focus();
            });
        }
    }
}
customElements.define('login-form', LoginFormComponent);
