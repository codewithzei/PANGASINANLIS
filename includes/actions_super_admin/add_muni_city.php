<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/muni_city.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['muni_city_status'] = 'error';
    $_SESSION['muni_city_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $muniCityName = trim($_POST['muni_city_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($muniCityName)) {
        $_SESSION['muni_city_status'] = 'error';
        $_SESSION['muni_city_message'] = 'Please enter a municipal/city name.';
        header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
        exit;
    }

    $muniCityObj = new MuniCity($pdo);
    $result = $muniCityObj->createMuniCity($muniCityName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'MUNI_CITY_MGMT',
            'CREATE',
            null,
            "Created municipality/city: $muniCityName"
        );
    }

    $_SESSION['muni_city_status'] = $result['status'];
    $_SESSION['muni_city_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
    exit;
}
?>