<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/requirement.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['requirement_status'] = 'error';
    $_SESSION['requirement_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/checklists');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requirementName = trim($_POST['requirement_name'] ?? '');

    $docTypeIds = isset($_POST['doc_type_ids']) && is_array($_POST['doc_type_ids'])
        ? array_map('intval', $_POST['doc_type_ids'])
        : [];

    $currentUserId = $_SESSION['user_id'];

    if (empty($requirementName)) {
        $_SESSION['requirement_status'] = 'error';
        $_SESSION['requirement_message'] = 'Please enter a requirement name.';
        header('Location: /PangasinanLIS/pages/admin/checklists');
        exit;
    }

    $requirementObj = new Requirement($pdo);
    $result = $requirementObj->createRequirement($requirementName, $docTypeIds, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'CHECKLIST_MGMT',
            'CREATE',
            null,
            "Created requirement: $requirementName"
        );
    }

    $_SESSION['requirement_status'] = $result['status'];
    $_SESSION['requirement_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/checklists');
    exit;
}
?>