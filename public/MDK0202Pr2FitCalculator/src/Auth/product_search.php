<?php
//AJAX-поиск продуктов — возвращает JSON
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(403); echo '[]'; exit; }

require_once '../Config/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo '[]'; exit; }

$userId = (int)$_SESSION['user_id'];
$q_safe = mysqli_real_escape_string($conn, $q);

//ищу по названию, сначала продукты этого пользователя потом все остальные
$sql = "SELECT productid AS id, product_name AS name, calories AS cal,
               protein AS prot, fat, carbs
        FROM products
        WHERE product_name LIKE '%$q_safe%'
        ORDER BY (created_by_userid = $userId) DESC, product_name ASC
        LIMIT 10";

$res  = mysqli_query($conn, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    //привожу числа к float чтобы JSON был корректным
    $rows[] = [
        'id'   => (int)$r['id'],
        'name' => $r['name'],
        'cal'  => (float)$r['cal'],
        'prot' => (float)$r['prot'],
        'fat'  => (float)$r['fat'],
        'carbs'=> (float)$r['carbs'],
    ];
}
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
