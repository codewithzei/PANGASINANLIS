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
    $roleName      = trim($_POST['role_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($roleName)) {
        $_SESSION['role_status'] = 'error';
        $_SESSION['role_message'] = 'Please enter a role name.';
        header('Location: /PangasinanLIS/pages/super_admin/user_roles');
        exit;
    }

    $roleObj = new Role($pdo);
    $result  = $roleObj->createRole($roleName, $currentUserId);

    if ($result['status'] === 'success') {
        // Get the newly created role ID
        $newRoleId = $pdo->lastInsertId() ?: null;

        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROLE_MGMT',
            'CREATE',
            $newRoleId,
            "Created new role: $roleName"
        );
    }

    $_SESSION['role_status']  = $result['status'];
    $_SESSION['role_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/user_roles');
    exit;
}
?>