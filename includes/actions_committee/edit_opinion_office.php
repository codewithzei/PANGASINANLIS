<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/opinion_office.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinion_office_status'] = 'error';
    $_SESSION['opinion_office_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/committee/opinion_offices');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opinionOfficeId   = intval($_POST['opinion_office_id'] ?? 0);
    $opinionOfficeCode = trim($_POST['opinion_office_code'] ?? '');
    $opinionOfficeName = trim($_POST['opinion_office_name'] ?? '');
    $status            = trim($_POST['status'] ?? 'active'); // Enum 'active' or 'inactive'
    $currentUserId     = $_SESSION['user_id'];

    if (empty($opinionOfficeId)) {
        $_SESSION['opinion_office_status'] = 'error';
        $_SESSION['opinion_office_message'] = 'Invalid opinion office ID.';
        header('Location: /PangasinanLIS/pages/committee/opinion_offices');
        exit;
    }

    if (empty($opinionOfficeCode) || empty($opinionOfficeName)) {
        $_SESSION['opinion_office_status'] = 'error';
        $_SESSION['opinion_office_message'] = 'Please enter both office code and name.';
        header('Location: /PangasinanLIS/pages/committee/opinion_offices');
        exit;
    }

    $officeObj = new OpinionOffice($pdo);
    $result    = $officeObj->updateOpinionOffice($opinionOfficeId, $opinionOfficeCode, $opinionOfficeName, $status, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_OFFICE_MGMT',
            'UPDATE',
            $opinionOfficeId,
            "Updated opinion office: $opinionOfficeName ($opinionOfficeCode)"
        );
    }

    $_SESSION['opinion_office_status']  = $result['status'];
    $_SESSION['opinion_office_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_offices');
    exit;
}
?>