<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/login.php'; ?>
<?php
    logout();
    redirect('login.php');
?>
