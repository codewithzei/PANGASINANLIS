<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/user.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_status'] = 'error';
    $_SESSION['user_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'];

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $roleId = $_POST['role_id'] ?? '';

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $middleName = trim($_POST['middle_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');

    $errors = [];

    if (empty($username)) { $errors[] = 'Username is required.'; }
    if (empty($email)) { $errors[] = 'Email is required.'; } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Invalid email format.'; }
    if (empty($password)) { $errors[] = 'Password is required.'; } elseif (strlen($password) < 8) { $errors[] = 'Password must be at least 8 characters long.'; }
    if (empty($roleId)) { $errors[] = 'Please select a user role.'; }
    if (empty($firstName)) { $errors[] = 'First name is required.'; }
    if (empty($lastName)) { $errors[] = 'Last name is required.'; }

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
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($fileObj['tmp_name'], $destination)) {
                $profilePicturePath = 'assets/profiles/' . $filename;
            } else {
                $errors[] = 'Failed to successfully upload the profile picture.';
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
    $result = $userObj->createUser(
        $username,
        $email,
        $password,
        $roleId,
        $firstName,
        $lastName,
        $middleName ?: null,
        $suffix ?: null,
        $contactNumber ?: null,
        $profilePicturePath,
        $currentUserId
    );

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $newUserId = $result['user_id'] ?? null;
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'USER_MGMT',
            'CREATE',
            $newUserId,
            "Created new user: $username ($firstName $lastName)"
        );
    }

    $_SESSION['user_status'] = $result['status'];
    $_SESSION['user_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/super_admin/user_accounts');
    exit;
}
?>