<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/external_office.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['external_office_status'] = 'error';
    $_SESSION['external_office_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/external_offices');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $externalOfficeId = intval($_POST['external_office_id'] ?? 0);
    $currentUserId    = $_SESSION['user_id'];

    if (empty($externalOfficeId)) {
        $_SESSION['external_office_status'] = 'error';
        $_SESSION['external_office_message'] = 'Invalid external office ID.';
        header('Location: /PangasinanLIS/pages/admin/external_offices');
        exit;
    }

    // Fetch name from DB for accurate audit label
    $officeName = "External Office #$externalOfficeId";
    try {
        $stmt = $pdo->prepare("SELECT external_office_name FROM external_offices WHERE external_office_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $externalOfficeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $officeName = $row['external_office_name']; }
    } catch (PDOException $e) {}

    $externalOfficeObj = new ExternalOffice($pdo);
    $result = $externalOfficeObj->deleteExternalOffice($externalOfficeId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'EXT_OFFICE_MGMT',
            'DELETE',
            $externalOfficeId,
            "Deleted external office: $officeName"
        );
    }

    $_SESSION['external_office_status']  = $result['status'];
    $_SESSION['external_office_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/external_offices');
    exit;
}
?>