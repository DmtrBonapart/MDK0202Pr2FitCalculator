<?php
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
csrf_verify();

$user_id  = (int)$_SESSION['user_id'];
$name     = trim($_POST['product_name'] ?? '');
$category = trim($_POST['category'] ?? '');
$calories = (float)($_POST['calories'] ?? 0);
$protein  = (float)($_POST['protein'] ?? 0);
$fat      = (float)($_POST['fat'] ?? 0);
$carbs    = (float)($_POST['carbs'] ?? 0);

if ($name === '' || $calories < 0 || $protein < 0 || $fat < 0 || $carbs < 0) {
    header('Location: ../Pages/products.php?error=invalid_data');
    exit;
}

$categoryValue = ($category !== '') ? $category : null;
$isVerified = 0;
$img_data = null;

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO products (product_name, category, calories, protein, fat, carbs, is_verified, created_by_userid, food_img)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log('product_add prepare failed: ' . mysqli_error($conn));
    header('Location: ../Pages/products.php?error=db_error');
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'ssddddiis',
    $name,
    $categoryValue,
    $calories,
    $protein,
    $fat,
    $carbs,
    $isVerified,
    $user_id,
    $img_data
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../Pages/products.php?success=added');
    exit;
}

error_log('product_add execute failed: ' . mysqli_stmt_error($stmt));
mysqli_stmt_close($stmt);
header('Location: ../Pages/products.php?error=db_error');
exit;
