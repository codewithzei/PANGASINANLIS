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
    $muniCityId   = intval($_POST['muni_city_id'] ?? 0);
    $muniCityName = trim($_POST['muni_city_name'] ?? '');
    $dataStatusId = intval($_POST['data_status_id'] ?? 1);
    $currentUserId = $_SESSION['user_id'];

    if (empty($muniCityId)) {
        $_SESSION['muni_city_status'] = 'error';
        $_SESSION['muni_city_message'] = 'Invalid municipality/city ID.';
        header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
        exit;
    }

    if (empty($muniCityName)) {
        $_SESSION['muni_city_status'] = 'error';
        $_SESSION['muni_city_message'] = 'Please enter a municipality/city name.';
        header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
        exit;
    }

    if ($dataStatusId <= 0) {
        $_SESSION['muni_city_status'] = 'error';
        $_SESSION['muni_city_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
        exit;
    }

    $muniCityObj = new MuniCity($pdo);
    $result = $muniCityObj->updateMuniCity($muniCityId, $muniCityName, $dataStatusId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'MUNI_CITY_MGMT',
            'UPDATE',
            $muniCityId,
            "Updated municipality/city: $muniCityName"
        );
    }

    $_SESSION['muni_city_status'] = $result['status'];
    $_SESSION['muni_city_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
    exit;
}
?>
