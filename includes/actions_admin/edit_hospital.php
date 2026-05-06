<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/hospital.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['hospital_status'] = 'error';
    $_SESSION['hospital_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/hospitals');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospitalId    = intval($_POST['hospital_id'] ?? 0);
    $hospitalName  = trim($_POST['hospital_name'] ?? '');
    $status        = intval($_POST['data_status_id'] ?? 1);
    $currentUserId = $_SESSION['user_id'];

    if (empty($hospitalId)) {
        $_SESSION['hospital_status'] = 'error';
        $_SESSION['hospital_message'] = 'Invalid hospital ID.';
        header('Location: /PangasinanLIS/pages/admin/hospitals');
        exit;
    }

    if (empty($hospitalName)) {
        $_SESSION['hospital_status'] = 'error';
        $_SESSION['hospital_message'] = 'Please enter a hospital name.';
        header('Location: /PangasinanLIS/pages/admin/hospitals');
        exit;
    }

    if ($status <= 0) {
        $_SESSION['hospital_status'] = 'error';
        $_SESSION['hospital_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/admin/hospitals');
        exit;
    }

    $hospitalObj = new Hospital($pdo);
    $result = $hospitalObj->updateHospital($hospitalId, $hospitalName, $status, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'HOSPITAL_MGMT',
            'UPDATE',
            $hospitalId,
            "Updated hospital: $hospitalName"
        );
    }

    $_SESSION['hospital_status'] = $result['status'];
    $_SESSION['hospital_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/hospitals');
    exit;
}
?>
