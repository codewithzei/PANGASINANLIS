<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../classes/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both username/email and password.';
        header('Location: ../pages/auth/login.php');
        exit;
    }

    $userObj = new User($pdo);
    $user = $userObj->getUserByUsernameOrEmail($usernameOrEmail);

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['account_status'] !== 'Active') {
            $_SESSION['login_error'] = 'Your account is ' . strtolower($user['account_status']) . '. Please contact the administrator.';
            header('Location: ../pages/auth/login');
            exit;
        }

        $userObj->updateLastLogin($user['user_account_id']);

        // Store session variables securely
        $_SESSION['user_id'] = $user['user_account_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['user_role_id'];
        $_SESSION['role_name'] = $user['user_role_name'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['profile_picture'] = $user['profile_picture'];

        // Use global dashboard instead of folder-based dashboard
        header("Location: ../pages/dashboard");
        exit;
    } else {
        $_SESSION['login_error'] = 'Invalid username or password.';
        header('Location: ../pages/auth/login.php');
        exit;
    }
} else {
    header('Location: ../pages/auth/login.php');
    exit;
}
?>