<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_status'] = 'error';
    $_SESSION['user_message'] = 'Unauthorized access. Please login.';
    header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'];

    $userId = $_POST['user_id'] ?? '';
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $middleName = trim($_POST['middle_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $roleId = $_POST['role_id'] ?? '';
    $status = $_POST['account_status'] ?? '';

    $errors = [];
    if (empty($userId)) $errors[] = 'User ID is required.';
    if (empty($firstName)) $errors[] = 'First name is required.';
    if (empty($lastName)) $errors[] = 'Last name is required.';
    if (empty($roleId)) $errors[] = 'Role is required.';
    if (empty($status)) $errors[] = 'Status is required.';

    $profilePicturePath = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileObj = $_FILES['profile_picture'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileObj['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Invalid profile picture format.';
        } elseif ($fileObj['size'] > $maxSize) {
            $errors[] = 'Profile picture exceeds the 2MB limit.';
        } else {
            $uploadDir = __DIR__ . '/../../assets/profiles/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
            $extension = strtolower(pathinfo($fileObj['name'], PATHINFO_EXTENSION));
            if (empty($extension)) { $extension = str_replace('image/', '', $mimeType); }
            $filename = uniqid('profile_') . '.' . $extension;
            if (move_uploaded_file($fileObj['tmp_name'], $uploadDir . $filename)) {
                $profilePicturePath = 'assets/profiles/' . $filename;
            } else {
                $errors[] = 'Failed to upload profile picture.';
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['user_status'] = 'error';
        $_SESSION['user_message'] = implode(' ', $errors);
        header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
        exit;
    }

    $userObj = new User($pdo);

    $existingUser = $userObj->getUserById($userId);
    if (!isset($existingUser['data'])) {
        $_SESSION['user_status'] = 'error';
        $_SESSION['user_message'] = 'User not found.';
        header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
        exit;
    }
    $existingPP = $existingUser['data']['profile_picture'] ?? null;
    $finalPP = $profilePicturePath !== null ? $profilePicturePath : $existingPP;
    $username = $existingUser['data']['username'] ?? "User #$userId";

    $resProfile = $userObj->updateUserProfile($userId, $firstName, $lastName, $middleName, $suffix, $contactNumber, $finalPP, $currentUserId);
    $resStatus  = $userObj->updateUserStatus($userId, $status, $currentUserId);
    $resRole    = $userObj->updateUserRole($userId, $roleId, $currentUserId);

    $audit = new AuditLogger($pdo);
    $audit->log(
        $currentUserId,
        'DATA_CHANGE',
        'USER_MGMT',
        'UPDATE',
        $userId,
        "Updated user: $username ($firstName $lastName)"
    );

    $_SESSION['user_status'] = 'success';
    $_SESSION['user_message'] = 'User updated successfully.';

    header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
    exit;
}
?>