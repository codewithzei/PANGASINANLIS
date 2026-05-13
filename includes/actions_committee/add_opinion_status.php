<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/opinion_status.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinion_status_status'] = 'error';
    $_SESSION['opinion_status_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opinionStatusName = trim($_POST['opinion_status_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($opinionStatusName)) {
        $_SESSION['opinion_status_status'] = 'error';
        $_SESSION['opinion_status_message'] = 'Please enter an opinion status name.';
        header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
        exit;
    }

    $opinionStatusObj = new OpinionStatus($pdo);
    $result = $opinionStatusObj->createOpinionStatus($opinionStatusName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_STATUS_MGMT',
            'CREATE',
            null,
            "Created opinion status: $opinionStatusName"
        );
    }

    $_SESSION['opinion_status_status'] = $result['status'];
    $_SESSION['opinion_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
    exit;
}
?>