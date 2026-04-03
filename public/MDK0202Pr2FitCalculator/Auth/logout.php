<?php
session_start();
session_destroy();
header('Location: ../Pages/landing.php');
exit;
?>