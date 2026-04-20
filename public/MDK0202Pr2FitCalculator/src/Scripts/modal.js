//управление модальными окнами
//модалка — всплывающее окно поверх страницы

//открыть модальное окно по id
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active'); //класс active показывает затемнение и само окно
        document.body.style.overflow = 'hidden'; //блокирую прокрутку страницы под модалкой
    }
}

//закрыть модальное окно по id
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; //возвращаю прокрутку
    }
}

//закрытие по клику на затемнённый фон
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        //нашли все открытые модалки и закрываем их
        document.querySelectorAll('.modal.active').forEach(m => {
            m.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});

//закрытие по нажатию Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(m => {
            m.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});
