class FooterComponent extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <footer class="footer">
                <div class="container footer-content">
                    <div class="footer-copyright">FitCalculator © 2025</div>
                    <div class="footer-links">
                        <a href="#">О проекте</a>
                        <a href="#">Помощь</a>
                        <a href="#">Контакты</a>
                    </div>
                    <div class="footer-version">Версия 1.0</div>
                </div>
            </footer>
        `;
    }
}

customElements.define('my-footer', FooterComponent);