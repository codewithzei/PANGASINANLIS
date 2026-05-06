<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/user.php';

// BAGO: Security Check - Pigilan ang mga unauthorized access
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userObj = new User($pdo);
    $result = $userObj->getUserById($userId);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "No user ID provided"]);
    exit;
}
?>