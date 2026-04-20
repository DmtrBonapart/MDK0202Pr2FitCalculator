<?php
//защита от CSRF-атак (Cross-Site Request Forgery)
//CSRF — атака когда злоумышленник заставляет браузер пользователя
//отправить запрос от его имени (например через скрытую форму на другом сайте)
//защита: генерируем уникальный токен, вставляем в форму, проверяем при отправке

//запускаю сессию если ещё не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//генерирую новый CSRF-токен если его ещё нет в сессии
function csrf_generate(): string {
    if (empty($_SESSION['csrf_token'])) {
        //random_bytes генерирует криптографически безопасные случайные байты
        //bin2hex переводит их в строку из шестнадцатеричных символов
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

//возвращаю скрытое поле формы с токеном
//вставляется в каждую форму (пример): echo csrf_field();
function csrf_field(): string {
    $token = csrf_generate();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

//проверяю что токен из формы совпадает с токеном в сессии
//если не совпадает — это возможная CSRF-атака, прерываю выполнение
function csrf_verify(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        //hash_equals — сравнение безопасное к timing-атакам
        http_response_code(403);
        die('Ошибка безопасности. Попробуйте снова.');
    }
    //после проверки обновляю токен чтобы нельзя было использовать его повторно
    unset($_SESSION['csrf_token']);
}
