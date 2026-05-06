<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/role.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['role_status'] = 'error';
    $_SESSION['role_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/user_roles');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roleId        = intval($_POST['role_id'] ?? 0);
    $currentUserId = $_SESSION['user_id'];

    if (empty($roleId)) {
        $_SESSION['role_status'] = 'error';
        $_SESSION['role_message'] = 'Invalid role ID.';
        header('Location: /PangasinanLIS/pages/super_admin/user_roles');
        exit;
    }

    // Fetch role name from DB before deleting so the audit log is always accurate
    $roleName = "Role #$roleId";
    try {
        $stmt = $pdo->prepare("SELECT user_role_name FROM user_roles WHERE user_role_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $roleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $roleName = $row['user_role_name'];
        }
    } catch (PDOException $e) {
        // Keep default label on error
    }

    $roleObj = new Role($pdo);
    $result  = $roleObj->deleteRole($roleId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROLE_MGMT',
            'DELETE',
            $roleId,
            "Deleted role: $roleName"
        );
    }

    $_SESSION['role_status']  = $result['status'];
    $_SESSION['role_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/user_roles');
    exit;
}
?>