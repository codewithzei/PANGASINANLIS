<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/external_office.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['external_office_status'] = 'error';
    $_SESSION['external_office_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/external_offices');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $externalOfficeId   = intval($_POST['external_office_id'] ?? 0);
    $externalOfficeName = trim($_POST['external_office_name'] ?? '');
    $status             = intval($_POST['data_status_id'] ?? 1);
    $currentUserId      = $_SESSION['user_id'];

    if (empty($externalOfficeId)) {
        $_SESSION['external_office_status'] = 'error';
        $_SESSION['external_office_message'] = 'Invalid external office ID.';
        header('Location: /PangasinanLIS/pages/admin/external_offices');
        exit;
    }

    if (empty($externalOfficeName)) {
        $_SESSION['external_office_status'] = 'error';
        $_SESSION['external_office_message'] = 'Please enter an external office name.';
        header('Location: /PangasinanLIS/pages/admin/external_offices');
        exit;
    }

    if ($status <= 0) {
        $_SESSION['external_office_status'] = 'error';
        $_SESSION['external_office_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/admin/external_offices');
        exit;
    }

    $externalOfficeObj = new ExternalOffice($pdo);
    $result = $externalOfficeObj->updateExternalOffice($externalOfficeId, $externalOfficeName, $status, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'EXT_OFFICE_MGMT',
            'UPDATE',
            $externalOfficeId,
            "Updated external office: $externalOfficeName"
        );
    }

    $_SESSION['external_office_status']  = $result['status'];
    $_SESSION['external_office_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/external_offices');
    exit;
}
?>