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
    $sourceTypeId  = intval($_POST['source_type_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($sourceTypeId)) {
        $_SESSION['source_type_status'] = 'error';
        $_SESSION['source_type_message'] = 'Invalid source type ID.';
        header('Location: /PangasinanLIS/pages/admin/source_types');
        exit;
    }

    // Fetch name from DB for accurate audit label
    $typeName = "Source Type #$sourceTypeId";
    try {
        $stmt = $pdo->prepare("SELECT source_type_name FROM source_types WHERE source_type_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $sourceTypeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $typeName = $row['source_type_name']; }
    } catch (PDOException $e) {}

    $sourceTypeObj = new SourceType($pdo);
    $result = $sourceTypeObj->deleteSourceType($sourceTypeId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'SOURCE_TYPE_MGMT',
            'DELETE',
            $sourceTypeId,
            "Deleted source type: $typeName"
        );
    }

    $_SESSION['source_type_status']  = $result['status'];
    $_SESSION['source_type_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/source_types');
    exit;
}
?>