<?php
// Redirect langsung ke halaman read.php
session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }
    
header("Location: dashboard_coba2.php");
exit;
?>