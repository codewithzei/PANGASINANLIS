<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/opinion_status.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinion_status_status'] = 'error';
    $_SESSION['opinion_status_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opinionStatusId = intval($_POST['opinion_status_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($opinionStatusId)) {
        $_SESSION['opinion_status_status'] = 'error';
        $_SESSION['opinion_status_message'] = 'Invalid opinion status ID.';
        header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
        exit;
    }

    // Fetch name from DB BEFORE deleting so audit log always has the real name
    $opinionStatusName = "Opinion Status #$opinionStatusId";
    try {
        $stmt = $pdo->prepare("SELECT opinion_status_name FROM opinion_statuses WHERE opinion_status_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $opinionStatusId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $opinionStatusName = $row['opinion_status_name']; }
    } catch (PDOException $e) {}

    $opinionStatusObj = new OpinionStatus($pdo);
    $result = $opinionStatusObj->deleteOpinionStatus($opinionStatusId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_STATUS_MGMT',
            'DELETE',
            $opinionStatusId,
            "Deleted opinion status: $opinionStatusName"
        );
    }

    $_SESSION['opinion_status_status'] = $result['status'];
    $_SESSION['opinion_status_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_statuses');
    exit;
}
?>