<?php
//удаление продукта из базы
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
csrf_verify();

$user_id    = (int)$_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    header('Location: ../Pages/products.php');
    exit;
}

//удаляю только если продукт добавил этот пользователь
$sql = "DELETE FROM products WHERE productid = $product_id AND created_by_userid = $user_id";
mysqli_query($conn, $sql);

header('Location: ../Pages/products.php');
exit;
