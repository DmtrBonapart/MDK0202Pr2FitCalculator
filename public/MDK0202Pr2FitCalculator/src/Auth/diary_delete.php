<?php
//удаление записи из дневника питания
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
csrf_verify();

$user_id  = (int)$_SESSION['user_id'];
$diary_id = (int)($_POST['diary_id'] ?? 0);
$date     = $_POST['entry_date'] ?? date('Y-m-d');

if ($diary_id <= 0) {
    header('Location: ../Pages/dashboard.php?date=' . urlencode($date));
    exit;
}

//удаляю только если запись принадлежит текущему пользователю
//это важно — без проверки userid чужой мог бы удалить чужие данные
$sql = "DELETE FROM fooddiary WHERE diaryid = $diary_id AND userid = $user_id";
mysqli_query($conn, $sql);

header('Location: ../Pages/dashboard.php?date=' . urlencode($date));
exit;
