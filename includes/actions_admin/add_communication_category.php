<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/communication_category.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['communication_category_status'] = 'error';
    $_SESSION['communication_category_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/communication_categories');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $communicationCategoryName = trim($_POST['communication_category_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($communicationCategoryName)) {
        $_SESSION['communication_category_status'] = 'error';
        $_SESSION['communication_category_message'] = 'Please enter a communication category name.';
        header('Location: /PangasinanLIS/pages/admin/communication_categories');
        exit;
    }

    $communicationCategoryObj = new CommunicationCategory($pdo);
    $result = $communicationCategoryObj->createCommunicationCategory($communicationCategoryName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'COMM_CATEGORY_MGMT',
            'CREATE',
            null,
            "Created communication category: $communicationCategoryName"
        );
    }

    $_SESSION['communication_category_status']  = $result['status'];
    $_SESSION['communication_category_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/communication_categories');
    exit;
}
?>