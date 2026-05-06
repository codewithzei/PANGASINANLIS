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
    $docStatId     = intval($_POST['document_status_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($docStatId)) {
        $_SESSION['document_status_status'] = 'error';
        $_SESSION['document_status_message'] = 'Invalid document status ID.';
        header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
        exit;
    }

    // Fetch name from DB BEFORE deleting so audit log always has the real name
    $docStatName = "Document Status #$docStatId";
    try {
        $stmt = $pdo->prepare("SELECT document_status_name FROM document_statuses WHERE document_status_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $docStatId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $docStatName = $row['document_status_name']; }
    } catch (PDOException $e) {}

    $docStatObj = new DocStat($pdo);
    $result = $docStatObj->deleteDocStat($docStatId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'DOC_STATUS_MGMT',
            'DELETE',
            $docStatId,
            "Deleted document status: $docStatName"
        );
    }

    $_SESSION['document_status_status'] = $result['status'];
    $_SESSION['document_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/document_statuses');
    exit;
}
?>