<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/doc_type.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['document_type_status'] = 'error';
    $_SESSION['document_type_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/document_types');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docTypeName = trim($_POST['document_type_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($docTypeName)) {
        $_SESSION['document_type_status'] = 'error';
        $_SESSION['document_type_message'] = 'Please enter a document type name.';
        header('Location: /PangasinanLIS/pages/super_admin/document_types');
        exit;
    }

    $docTypeObj = new DocType($pdo);
    $result = $docTypeObj->createDocType($docTypeName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'DOC_TYPE_MGMT',
            'CREATE',
            null,
            "Created document type: $docTypeName"
        );
    }

    $_SESSION['document_type_status'] = $result['status'];
    $_SESSION['document_type_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/document_types');
    exit;
}
?>