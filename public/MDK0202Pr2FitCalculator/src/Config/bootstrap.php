<?php
// =============================================================
// Файл: bootstrap.php
// Назначение: общий старт для страниц и обработчиков.
// Здесь запускаю сессию и подключаю базовые утилиты проекта.
// =============================================================

//запускаю сессию если ещё не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//подключаю часто используемые файлы через __DIR__ чтобы пути не ломались
//
// важно: include/require не должен "выводить" текст в браузер.
// если вдруг какой-то подключаемый файл сломан (например, потерялся <?php),
// PHP выведет его содержимое как обычный текст. Чтобы такое сразу отловить,
// подключаю через буфер и даю понятную ошибку.
//
// иногда в локальных сборках включён OPcache, и он может держать старую версию файла.
// перед подключением пробую сбросить кэш для helpers/csrf.
$helpersPath = __DIR__ . '/helpers.php';
$csrfPath    = __DIR__ . '/csrf.php';
clearstatcache(true, $helpersPath);
clearstatcache(true, $csrfPath);
if (function_exists('opcache_invalidate')) {
    @opcache_invalidate($helpersPath, true);
    @opcache_invalidate($csrfPath, true);
}
ob_start();
require_once $helpersPath;
require_once $csrfPath;
$bootstrapOutput = ob_get_clean();

//если при подключении что-то вывелось — это почти всегда ошибка структуры файла
if (!empty($bootstrapOutput)) {
    http_response_code(500);
    //отдаю как простой текст, чтобы браузер не "съедал" символы <...>
    header('Content-Type: text/plain; charset=utf-8');
    echo "Ошибка подключения файлов (bootstrap).\n";
    echo "Как правило, это значит что один из подключаемых PHP-файлов сломан (например нет PHP-открывающего тега).\n\n";
    echo "bootstrap: " . __FILE__ . "\n";
    echo "helpers:   " . realpath(__DIR__ . '/helpers.php') . "\n";
    echo "csrf:      " . realpath(__DIR__ . '/csrf.php') . "\n\n";
    echo "Что вывелось при подключении (первые 400 символов):\n";
    echo substr($bootstrapOutput, 0, 400);
    exit;
}

//контрольная проверка, что функции реально загрузились
if (!function_exists('csrf_field') || !function_exists('csrf_verify')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Ошибка: CSRF-функции не загрузились.\n";
    echo "csrf файл: " . realpath(__DIR__ . '/csrf.php') . "\n";
    exit;
}

