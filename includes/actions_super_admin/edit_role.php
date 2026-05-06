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
    $roleId = intval($_POST['role_id'] ?? 0);
    $roleName = trim($_POST['role_name'] ?? '');
    $dataStatusId = intval($_POST['data_status_id'] ?? 1);
    $currentUserId = $_SESSION['user_id'];

    if (empty($roleId)) {
        $_SESSION['role_status'] = 'error';
        $_SESSION['role_message'] = 'Invalid role ID.';
        header('Location: /PangasinanLIS/pages/super_admin/user_roles');
        exit;
    }

    if (empty($roleName)) {
        $_SESSION['role_status'] = 'error';
        $_SESSION['role_message'] = 'Please enter a role name.';
        header('Location: /PangasinanLIS/pages/super_admin/user_roles');
        exit;
    }

    if ($dataStatusId <= 0) {
        $_SESSION['role_status'] = 'error';
        $_SESSION['role_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/super_admin/user_roles');
        exit;
    }

    $roleObj = new Role($pdo);
    $result = $roleObj->updateRole($roleId, $roleName, $dataStatusId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROLE_MGMT',
            'UPDATE',
            $roleId,
            "Updated role: $roleName"
        );
    }

    $_SESSION['role_status'] = $result['status'];
    $_SESSION['role_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/user_roles');
    exit;
}
?>