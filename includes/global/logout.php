<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    $errors = [];
    
    if (empty($usernameOrEmail)) {
        $errors[] = 'Username or email is required.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }
    
    if (!empty($errors)) {
        $_SESSION['login_status'] = 'error';
        $_SESSION['login_message'] = implode(' ', $errors);
        header('Location: /PangasinanLIS/pages/auth/login');
        exit;
    }
    
    // Attempt login
    $userObj = new User($pdo);
    $user = $userObj->getUserByUsernameOrEmail($usernameOrEmail);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Check account status
        if ($user['account_status'] !== 'Active') {
            $_SESSION['login_status'] = 'error';
            $_SESSION['login_message'] = 'Your account is ' . strtolower($user['account_status']) . '. Please contact administrator.';
            header('Location: /PangasinanLIS/pages/auth/login');
            exit;
        }
        
        // Update last login
        $userObj->updateLastLogin($user['user_account_id']);
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_account_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role_id'] = $user['user_role_id'];
        $_SESSION['role_name'] = $user['user_role_name'];
        
        header('Location: /PangasinanLIS/pages/dashboard');
        exit;
    } else {
        $_SESSION['login_status'] = 'error';
        $_SESSION['login_message'] = 'Invalid username/email or password.';
        header('Location: /PangasinanLIS/pages/auth/login');
        exit;
    }
}
?>