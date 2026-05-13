<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/opinion_office.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['opinion_office_status'] = 'error';
    $_SESSION['opinion_office_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/committee/opinion_offices');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opinionOfficeId = intval($_POST['opinion_office_id'] ?? 0);
    $currentUserId   = $_SESSION['user_id'];

    if (empty($opinionOfficeId)) {
        $_SESSION['opinion_office_status'] = 'error';
        $_SESSION['opinion_office_message'] = 'Invalid opinion office ID.';
        header('Location: /PangasinanLIS/pages/committee/opinion_offices');
        exit;
    }

    // Fetch opinion office name from DB before deleting so the audit log is always accurate
    $officeName = "Opinion Office #$opinionOfficeId";
    try {
        $stmt = $pdo->prepare("SELECT opinion_office_name FROM opinion_offices WHERE opinion_office_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $opinionOfficeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $officeName = $row['opinion_office_name'];
        }
    } catch (PDOException $e) {
        // Keep default label on error
    }

    $officeObj = new OpinionOffice($pdo);
    $result    = $officeObj->deleteOpinionOffice($opinionOfficeId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'OPINION_OFFICE_MGMT',
            'DELETE',
            $opinionOfficeId,
            "Deleted opinion office: $officeName"
        );
    }

    $_SESSION['opinion_office_status']  = $result['status'];
    $_SESSION['opinion_office_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/committee/opinion_offices');
    exit;
}
?>