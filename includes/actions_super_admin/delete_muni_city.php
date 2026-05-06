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
    $muniCityId    = intval($_POST['muni_city_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($muniCityId)) {
        $_SESSION['muni_city_status'] = 'error';
        $_SESSION['muni_city_message'] = 'Invalid municipality/city ID.';
        header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
        exit;
    }

    // Fetch name from DB BEFORE deleting so audit log always has the real name
    $muniCityName = "Municipality/City #$muniCityId";
    try {
        $stmt = $pdo->prepare("SELECT muni_city_name FROM muni_cities WHERE muni_city_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $muniCityId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $muniCityName = $row['muni_city_name']; }
    } catch (PDOException $e) {}

    $muniCityObj = new MuniCity($pdo);
    $result = $muniCityObj->deleteMuniCity($muniCityId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'MUNI_CITY_MGMT',
            'DELETE',
            $muniCityId,
            "Deleted municipality/city: $muniCityName"
        );
    }

    $_SESSION['muni_city_status'] = $result['status'];
    $_SESSION['muni_city_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/muni_cities');
    exit;
}
?>