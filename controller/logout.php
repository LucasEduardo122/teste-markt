<?php
session_start();

if (empty($_SESSION['ID_USER'])) {
    return header('location: ../login.php');
}

session_destroy();

header('location: ../login.php');
exit();
?>