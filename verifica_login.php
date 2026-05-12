<?php
session_start();

if(
    !isset($_SESSION['logado']) ||
    !isset($_SERVER['HTTP_REFERER'])
){
    session_destroy();

    header("Location: login.php");

    exit();
}
?>