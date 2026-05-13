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
    $opinionStatusId   = intval($_POST['opinion_status_id'] ?? 0);
    $opinionStatusName = trim($_POST['opinion_status_name'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($opinionStatusId)) {
        $_SESSION['opinion_status_status'] = 'error';
        $_SESSION['opinion_status_message'] = 'Invalid opinion status ID.';
        header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
        exit;
    }

    if (empty($opinionStatusName)) {
        $_SESSION['opinion_status_status'] = 'error';
        $_SESSION['opinion_status_message'] = 'Please enter an opinion status name.';
        header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
        exit;
    }

    if ($status <= 0) {
        $_SESSION['opinion_status_status'] = 'error';
        $_SESSION['opinion_status_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
        exit;
    }

    $opinionStatusObj = new OpinionStatus($pdo);
    $result = $opinionStatusObj->updateOpinionStatus($opinionStatusId, $opinionStatusName, $status, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_STATUS_MGMT',
            'UPDATE',
            $opinionStatusId,
            "Updated opinion status: $opinionStatusName"
        );
    }

    $_SESSION['opinion_status_status'] = $result['status'];
    $_SESSION['opinion_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
    exit;
}
?>
