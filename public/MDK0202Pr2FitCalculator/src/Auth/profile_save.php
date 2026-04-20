<?php
require_once __DIR__ . '/../Config/bootstrap.php';
require_once __DIR__ . '/../Config/db_connect.php';

if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
csrf_verify();

$user_id        = (int)$_SESSION['user_id'];
$full_name      = trim($_POST['full_name'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$birth_date     = trim($_POST['birth_date'] ?? '');
$current_weight = (float)($_POST['current_weight'] ?? 0);
$target_weight  = (float)($_POST['target_weight'] ?? 0);
$height         = (float)($_POST['height'] ?? 0);
$activity_level = (int)($_POST['activity_level'] ?? 1);

if ($full_name === '' || $height <= 0 || $current_weight <= 0) {
    header('Location: ../Pages/profile.php?error=invalid_data');
    exit;
}

$allowedGender = ['male', 'female', 'other'];
if (!in_array($gender, $allowedGender, true)) { $gender = 'other'; }
if ($activity_level < 1 || $activity_level > 5) { $activity_level = 1; }

$fn_s  = mysqli_real_escape_string($conn, $full_name);
$gen_s = mysqli_real_escape_string($conn, $gender);
$bd_s  = mysqli_real_escape_string($conn, $birth_date);

$hasBirthDate = false;
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM userdata LIKE 'birth_date'");
if ($colRes && mysqli_num_rows($colRes) > 0) { $hasBirthDate = true; }

$bd_sql = $birth_date !== '' ? "'$bd_s'" : 'NULL';
$age = 25;
if ($birth_date !== '') {
    try {
        $birth = new DateTime($birth_date);
        $now = new DateTime();
        $age = (int)$now->diff($birth)->y;
        if ($age <= 0 || $age > 120) { $age = 25; }
    } catch (Exception $e) {
        $age = 25;
    }
}
$age_sql = (string)$age;

$check = mysqli_query($conn, "SELECT dataid FROM userdata WHERE userid = $user_id");

if (mysqli_num_rows($check) > 0) {
    if ($hasBirthDate) {
        $sql = "UPDATE userdata SET
                    full_name='$fn_s', gender='$gen_s', age=$age_sql, birth_date=$bd_sql,
                    current_weight=$current_weight, target_weight=$target_weight,
                    height=$height, activity_level=$activity_level
                WHERE userid=$user_id";
    } else {
        $sql = "UPDATE userdata SET
                    full_name='$fn_s', gender='$gen_s', age=$age_sql,
                    current_weight=$current_weight, target_weight=$target_weight,
                    height=$height, activity_level=$activity_level
                WHERE userid=$user_id";
    }
} else {
    if ($hasBirthDate) {
        $sql = "INSERT INTO userdata (userid, full_name, gender, age, birth_date, current_weight, target_weight, height, activity_level)
                VALUES ($user_id, '$fn_s', '$gen_s', $age_sql, $bd_sql, $current_weight, $target_weight, $height, $activity_level)";
    } else {
        $sql = "INSERT INTO userdata (userid, full_name, gender, age, current_weight, target_weight, height, activity_level)
                VALUES ($user_id, '$fn_s', '$gen_s', $age_sql, $current_weight, $target_weight, $height, $activity_level)";
    }
}

$ok = mysqli_query($conn, $sql);
if (!$ok) {
    header('Location: ../Pages/profile.php?error=db_error');
    exit;
}

$_SESSION['user_name'] = $full_name;

header('Location: ../Pages/profile.php?success=saved');
exit;
