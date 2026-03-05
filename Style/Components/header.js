class HeaderComponent extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <header class="header">
                FitCalculator Header
            </header>
        `;
    }
}

customElements.define('my-header', HeaderComponent);