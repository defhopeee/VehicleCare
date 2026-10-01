<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function require_admin(){
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: '.SITE_URL.'/admin/login.php'); exit;
    }
}
function is_admin(){ return !empty($_SESSION['user']) && $_SESSION['user']['role']==='admin'; }
?>