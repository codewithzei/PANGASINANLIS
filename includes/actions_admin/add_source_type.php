<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/source_type.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['source_type_status'] = 'error';
    $_SESSION['source_type_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/source_types');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceTypeName = trim($_POST['source_type_name'] ?? '');
    $currentUserId  = $_SESSION['user_id'];

    if (empty($sourceTypeName)) {
        $_SESSION['source_type_status'] = 'error';
        $_SESSION['source_type_message'] = 'Please enter a source type name.';
        header('Location: /PangasinanLIS/pages/admin/source_types');
        exit;
    }

    $sourceTypeObj = new SourceType($pdo);
    $result = $sourceTypeObj->createSourceType($sourceTypeName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'SOURCE_TYPE_MGMT',
            'CREATE',
            null,
            "Created source type: $sourceTypeName"
        );
    }

    $_SESSION['source_type_status']  = $result['status'];
    $_SESSION['source_type_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/source_types');
    exit;
}
?>