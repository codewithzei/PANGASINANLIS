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
    $communicationCategoryId = intval($_POST['communication_category_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($communicationCategoryId)) {
        $_SESSION['communication_category_status'] = 'error';
        $_SESSION['communication_category_message'] = 'Invalid communication category ID.';
        header('Location: /PangasinanLIS/pages/admin/communication_categories');
        exit;
    }

    // Fetch name from DB for accurate audit label
    $categoryName = "Category #$communicationCategoryId";
    try {
        $stmt = $pdo->prepare("SELECT communication_category_name FROM communication_categories WHERE communication_category_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $communicationCategoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $categoryName = $row['communication_category_name']; }
    } catch (PDOException $e) {}

    $communicationCategoryObj = new CommunicationCategory($pdo);
    $result = $communicationCategoryObj->deleteCommunicationCategory($communicationCategoryId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'COMM_CATEGORY_MGMT',
            'DELETE',
            $communicationCategoryId,
            "Deleted communication category: $categoryName"
        );
    }

    $_SESSION['communication_category_status']  = $result['status'];
    $_SESSION['communication_category_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/communication_categories');
    exit;
}
?>