<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_status'] = 'error';
    $_SESSION['user_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/view_users');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $userName = trim($_POST['user_name'] ?? '');
    $currentUserId = $_SESSION['user_id'];

    if (empty($userId)) {
        $_SESSION['user_status'] = 'error';
        $_SESSION['user_message'] = 'User ID is required.';
        header('Location: /PangasinanLIS/pages/super_admin/view_users');
        exit;
    }

    $userObj = new User($pdo);

    // Fetch name before deleting for audit details
    if (empty($userName)) {
        $existing = $userObj->getUserById($userId);
        if (isset($existing['data'])) {
            $d = $existing['data'];
            $userName = trim($d['first_name'] . ' ' . $d['last_name']);
            if (empty($userName)) {
                $userName = $d['username'] ?? "User #$userId";
            }
        } else {
            $userName = "User #$userId";
        }
    }

    $result = $userObj->deleteUser($userId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'USER_MGMT',
            'DELETE',
            $userId,
            "Deleted user: $userName"
        );
    }

    $_SESSION['user_status'] = $result['status'];
    $_SESSION['user_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/view_users');
    exit;
}
?>