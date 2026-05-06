<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/hospital.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['hospital_status'] = 'error';
    $_SESSION['hospital_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/hospitals');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospitalId    = intval($_POST['hospital_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($hospitalId)) {
        $_SESSION['hospital_status'] = 'error';
        $_SESSION['hospital_message'] = 'Invalid hospital ID.';
        header('Location: /PangasinanLIS/pages/admin/hospitals');
        exit;
    }

    // Fetch name from DB for accurate audit label
    $hospitalName = "Hospital #$hospitalId";
    try {
        $stmt = $pdo->prepare("SELECT hospital_name FROM hospitals WHERE hospital_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $hospitalId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $hospitalName = $row['hospital_name']; }
    } catch (PDOException $e) {}

    $hospitalObj = new Hospital($pdo);
    $result      = $hospitalObj->deleteHospital($hospitalId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'HOSPITAL_MGMT',
            'DELETE',
            $hospitalId,
            "Deleted hospital: $hospitalName"
        );
    }

    $_SESSION['hospital_status']  = $result['status'];
    $_SESSION['hospital_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/hospitals');
    exit;
}
?>
