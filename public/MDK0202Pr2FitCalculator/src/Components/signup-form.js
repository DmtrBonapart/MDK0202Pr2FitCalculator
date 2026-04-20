//Web Component <signup-form> — форма регистрации
class SignupFormComponent extends HTMLElement {
    connectedCallback() {
        const csrf = this.getAttribute('csrf-field') || '';
        this.innerHTML = `
            <div class="auth-card">
                <h1 class="auth-title">Создать аккаунт</h1>
                <p class="auth-subtitle">Заполните форму для регистрации</p>
                <form class="auth-form" method="POST" action="../Auth/signup_process.php">
                    ${csrf}
                    <div class="input-group">
                        <input type="text" name="name" class="input-field"
                               placeholder="Введите ваше имя" required>
                        <img src="../Images/Icons/usernormal.png" class="input-icon" alt="">
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" class="input-field"
                               placeholder="Введите ваш email" required autocomplete="email">
                        <img src="../Images/Icons/EmailIconNormal.png" class="input-icon" alt="">
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" class="input-field"
                               placeholder="Пароль (минимум 6 символов)" required minlength="6"
                               autocomplete="new-password">
                        <button type="button" class="input-icon-btn" data-toggle="password" aria-label="Показать пароль">
                            <span class="eye-wrap is-hidden" style="display:flex;align-items:center;justify-content:center;">
                                <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="" class="eye-icon">
                            </span>
                        </button>
                    </div>
                    <div class="input-group">
                        <input type="password" name="confirm_password" class="input-field"
                               placeholder="Подтвердите пароль" required minlength="6"
                               autocomplete="new-password">
                        <button type="button" class="input-icon-btn" data-toggle="confirm_password" aria-label="Показать пароль">
                            <span class="eye-wrap is-hidden" style="display:flex;align-items:center;justify-content:center;">
                                <img src="../Images/Icons/SeePasswordIcon Normal.png" alt="" class="eye-icon">
                            </span>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Зарегистрироваться</button>
                </form>
                <div class="auth-footer-text">
                    Уже есть аккаунт? <a href="login.php" class="link-green">Войти</a>
                </div>
            </div>
        `;

        //переключатели видимости пароля (глазики)
        const bindToggle = (inputName, toggleName) => {
            const input  = this.querySelector(`input[name="${inputName}"]`);
            const btn    = this.querySelector(`[data-toggle="${toggleName}"]`);
            const eyeWrap = btn?.querySelector('.eye-wrap');
            if (!input || !btn || !eyeWrap) return;

            btn.addEventListener('click', () => {
                const isHidden = input.type === 'password';
                if (isHidden) {
                    input.type = 'text';
                    eyeWrap.classList.remove('is-hidden');
                    btn.setAttribute('aria-label', 'Скрыть пароль');
                } else {
                    input.type = 'password';
                    eyeWrap.classList.add('is-hidden');
                    btn.setAttribute('aria-label', 'Показать пароль');
                }
                input.focus();
            });
        };

        bindToggle('password', 'password');
        bindToggle('confirm_password', 'confirm_password');
    }
}
customElements.define('signup-form', SignupFormComponent);
