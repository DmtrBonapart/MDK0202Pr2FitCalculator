//интерактивный календарь для сайдбара
//показывает текущий месяц, выделяет сегодня, позволяет переключать месяцы

class SidebarCalendar {
    constructor(containerId, onDateSelect, initialDateStr = '') {
        this.container = document.getElementById(containerId);
        this.onDateSelect = onDateSelect; //функция-обработчик выбора даты
        this.storageKey = 'fitcalculator_selected_date';

        //устанавливаю выбранную дату:
        //- если страница передала initialDateStr (yyyy-mm-dd) — беру её
        //- иначе беру сохранённую дату из localStorage
        //- если и её нет, беру сегодняшнюю
        const today = new Date();
        let selected = today;
        const storedDateStr = this._getStoredDate();
        if (initialDateStr && /^\d{4}-\d{2}-\d{2}$/.test(initialDateStr)) {
            selected = new Date(initialDateStr + 'T00:00:00');
        } else if (storedDateStr) {
            selected = new Date(storedDateStr + 'T00:00:00');
        }
        this.selectedDate = selected;

        //текущий месяц календаря открываю по выбранной дате
        this.currentDate = new Date(selected.getFullYear(), selected.getMonth(), 1);

        this._saveDate(this._formatDate(this.selectedDate));
        this._syncDateLinks();

        if (this.container) this.render();
    }

    //отрисовка календаря
    render() {
        const year  = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const today = new Date();

        //названия месяцев на русском
        const monthNames = ['Январь','Февраль','Март','Апрель','Май','Июнь',
                            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
        const dayNames = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];

        //первый день месяца (0=вс, 1=пн ... переделываю под понедельник как начало)
        let firstDay = new Date(year, month, 1).getDay();
        firstDay = firstDay === 0 ? 6 : firstDay - 1; //0=пн, 6=вс

        //количество дней в месяце
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        //строю HTML календаря
        let html = `
        <div class="cal-header">
            <button class="cal-nav" id="calPrev">&larr;</button>
            <span class="cal-title">${monthNames[month]} ${year}</span>
            <button class="cal-nav" id="calNext">&rarr;</button>
        </div>
        <div class="cal-grid">
            ${dayNames.map(d => `<div class="cal-day-name">${d}</div>`).join('')}
        `;

        //пустые ячейки перед первым днём
        for (let i = 0; i < firstDay; i++) {
            html += `<div class="cal-cell cal-empty"></div>`;
        }

        //дни месяца
        for (let d = 1; d <= daysInMonth; d++) {
            const date    = new Date(year, month, d);
            const isToday = this._sameDay(date, today);
            const isSel   = this._sameDay(date, this.selectedDate);

            let cls = 'cal-cell';
            if (isToday) cls += ' cal-today';
            if (isSel)   cls += ' cal-selected';
            //выходные — суббота(6) и воскресенье(0)
            if (date.getDay() === 6 || date.getDay() === 0) cls += ' cal-weekend';

            const dateStr = this._formatDate(date); //yyyy-mm-dd для передачи на сервер
            html += `<div class="${cls}" data-date="${dateStr}">${d}</div>`;
        }

        html += `</div>`;
        this.container.innerHTML = html;

        //обработчики кнопок переключения месяца
        this.container.querySelector('#calPrev').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.render();
        });
        this.container.querySelector('#calNext').addEventListener('click', () => {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.render();
        });

        //клик по дню — выбор даты
        this.container.querySelectorAll('.cal-cell:not(.cal-empty)').forEach(cell => {
            cell.addEventListener('click', () => {
                const dateStr = cell.getAttribute('data-date');
                this.selectedDate = new Date(dateStr + 'T00:00:00');
                this._saveDate(dateStr);
                this._syncDateLinks(dateStr);
                this.render(); //перерисовываю чтобы обновить выделение
                if (this.onDateSelect) this.onDateSelect(dateStr);
            });
        });
    }

    //проверка что две даты в один день
    _sameDay(a, b) {
        return a.getFullYear() === b.getFullYear() &&
               a.getMonth()    === b.getMonth()    &&
               a.getDate()     === b.getDate();
    }

    //форматирую дату в yyyy-mm-dd для передачи в PHP
    _formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    //форматирую дату в дд/мм/гггг для отображения пользователю
    formatDisplay(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    getSelectedDateStr() {
        return this._formatDate(this.selectedDate);
    }

    _getStoredDate() {
        try {
            const value = localStorage.getItem(this.storageKey) || '';
            return /^\d{4}-\d{2}-\d{2}$/.test(value) ? value : '';
        } catch (e) {
            return '';
        }
    }

    _saveDate(dateStr) {
        try {
            localStorage.setItem(this.storageKey, dateStr);
        } catch (e) {}
    }

    _syncDateLinks(dateStr = this.getSelectedDateStr()) {
        document.querySelectorAll('[data-calendar-date-link]').forEach(link => {
            const baseHref = link.getAttribute('data-calendar-date-link') || link.getAttribute('href') || '';
            if (!baseHref) return;
            link.setAttribute('href', `${baseHref}?date=${encodeURIComponent(dateStr)}`);
        });
    }
}
