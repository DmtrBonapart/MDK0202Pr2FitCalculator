<?php
//подключение к базе данных MySQL
//подключается через require_once во всех скриптах которым нужна БД

$host    = '127.0.1.30'; //адрес сервера Open Server Panel
$user    = 'root';
$pass    = '';
$db_name = 'SletnevDB';

//устанавливаю соединение с MySQL
$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

//кодировка utf8mb4 — поддержка русского и эмодзи
mysqli_set_charset($conn, 'utf8mb4');
