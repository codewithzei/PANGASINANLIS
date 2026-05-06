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
    $docStatName = trim($_POST['document_status_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($docStatName)) {
        $_SESSION['document_status_status'] = 'error';
        $_SESSION['document_status_message'] = 'Please enter a document status name.';
        header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
        exit;
    }

    $docStatObj = new DocStat($pdo);
    $result = $docStatObj->createDocStat($docStatName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'DOC_STATUS_MGMT',
            'CREATE',
            null,
            "Created document status: $docStatName"
        );
    }

    $_SESSION['document_status_status'] = $result['status'];
    $_SESSION['document_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
    exit;
}
?>