<?php
//добавление приёма пищи в дневник питания
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
csrf_verify();

$user_id       = (int)$_SESSION['user_id'];
$product_id    = (int)($_POST['product_id']    ?? 0);
$serving_weight= (float)($_POST['serving_weight'] ?? 0);
$entry_date    = $_POST['entry_date'] ?? date('Y-m-d');

//проверяю что граммовка положительная
if ($product_id <= 0 || $serving_weight <= 0) {
    header('Location: ../Pages/dashboard.php?error=invalid_data&date=' . urlencode($entry_date));
    exit;
}

//проверяю что продукт существует в БД
$pid = (int)$product_id;
$check = mysqli_query($conn, "SELECT productid FROM products WHERE productid = $pid");
if (mysqli_num_rows($check) === 0) {
    header('Location: ../Pages/dashboard.php?error=product_not_found&date=' . urlencode($entry_date));
    exit;
}

$date_safe   = mysqli_real_escape_string($conn, $entry_date);
$weight_safe = round($serving_weight, 2);

$sql = "INSERT INTO fooddiary (userid, entry_date, productid, serving_weight, created_at)
        VALUES ($user_id, '$date_safe', $pid, $weight_safe, NOW())";

if (mysqli_query($conn, $sql)) {
    header('Location: ../Pages/dashboard.php?date=' . urlencode($entry_date));
} else {
    header('Location: ../Pages/dashboard.php?error=db_error&date=' . urlencode($entry_date));
}
exit;
