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
    $opinionOfficeCode = trim($_POST['opinion_office_code'] ?? '');
    $opinionOfficeName = trim($_POST['opinion_office_name'] ?? '');
    $currentUserId     = $_SESSION['user_id'];

    if (empty($opinionOfficeCode) || empty($opinionOfficeName)) {
        $_SESSION['opinion_office_status'] = 'error';
        $_SESSION['opinion_office_message'] = 'Please fill in all required fields.';
        header('Location: /PangasinanLIS/pages/committee/opinion_offices');
        exit;
    }

    $officeObj = new OpinionOffice($pdo);
    $result    = $officeObj->createOpinionOffice($opinionOfficeCode, $opinionOfficeName, $currentUserId);

    if ($result['status'] === 'success') {
        $newOfficeId = $result['new_id'] ?? null;

        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_OFFICE_MGMT', // Module Name
            'CREATE',
            $newOfficeId,
            "Created new opinion office: $opinionOfficeName ($opinionOfficeCode)"
        );
    }

    $_SESSION['opinion_office_status']  = $result['status'];
    $_SESSION['opinion_office_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_offices');
    exit;
}