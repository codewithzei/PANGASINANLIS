<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/doc_stat.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['document_status_status'] = 'error';
    $_SESSION['document_status_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docStatId   = intval($_POST['document_status_id'] ?? 0);
    $docStatName = trim($_POST['document_status_name'] ?? '');
    $dataStatusId = intval($_POST['data_status_id'] ?? 1);
    $currentUserId = $_SESSION['user_id'];

    if (empty($docStatId)) {
        $_SESSION['document_status_status'] = 'error';
        $_SESSION['document_status_message'] = 'Invalid document status ID.';
        header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
        exit;
    }

    if (empty($docStatName)) {
        $_SESSION['document_status_status'] = 'error';
        $_SESSION['document_status_message'] = 'Please enter a document status name.';
        header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
        exit;
    }

    if ($dataStatusId <= 0) {
        $_SESSION['document_status_status'] = 'error';
        $_SESSION['document_status_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
        exit;
    }

    $docStatObj = new DocStat($pdo);
    $result = $docStatObj->updateDocStat($docStatId, $docStatName, $dataStatusId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'DOC_STATUS_MGMT',
            'UPDATE',
            $docStatId,
            "Updated document status: $docStatName"
        );
    }

    $_SESSION['document_status_status'] = $result['status'];
    $_SESSION['document_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
    exit;
}
?>
