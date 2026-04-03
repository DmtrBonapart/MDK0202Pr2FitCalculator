<?php

$host = '127.0.1.30'; 
$user = 'root';      
$pass = '';          
$db_name = 'SletnevDB'; 

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

?>