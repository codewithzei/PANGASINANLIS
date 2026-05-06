<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role_name'])) {
    $dashboardUri = "pages/dashboard.php";
    header("Location: {$dashboardUri}");
} else {
    header('Location: pages/auth/login');
}
exit;
?>