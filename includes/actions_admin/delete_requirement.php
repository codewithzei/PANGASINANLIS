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
    $requirementId = intval($_POST['requirement_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($requirementId)) {
        $_SESSION['requirement_status'] = 'error';
        $_SESSION['requirement_message'] = 'Invalid requirement ID.';
        header('Location: /PangasinanLIS/pages/admin/checklists');
        exit;
    }

    // Fetch name from DB BEFORE deleting so audit log always has the real name
    $requirementName = "Requirement #$requirementId";
    try {
        $stmt = $pdo->prepare("SELECT requirement_name FROM requirements WHERE requirement_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $requirementId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $requirementName = $row['requirement_name']; }
    } catch (PDOException $e) {}

    $requirementObj = new Requirement($pdo);
    $result = $requirementObj->deleteRequirement($requirementId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'CHECKLIST_MGMT',
            'DELETE',
            $requirementId,
            "Deleted requirement: $requirementName"
        );
    }

    $_SESSION['requirement_status'] = $result['status'];
    $_SESSION['requirement_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/checklists');
    exit;
}
?>