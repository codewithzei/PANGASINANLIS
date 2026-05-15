<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$roleName = $_SESSION['role_name'] ?? '';
$userId   = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$counts = [];

try {
    if ($roleName === 'SP Secretary') {
        // Inbox: documents routed to SP Secretary (id=1) with no owner
        $stmt = $pdo->prepare("
            SELECT COUNT(document_id) FROM documents
            WHERE current_owner_user_id IS NULL
            AND current_routing_option_id = 1
        ");
        $stmt->execute();
        $counts['inbox'] = (int) $stmt->fetchColumn();

        // Received: documents owned by this user
        $stmt2 = $pdo->prepare("
            SELECT COUNT(document_id) FROM documents
            WHERE current_owner_user_id = :user_id
        ");
        $stmt2->execute([':user_id' => $userId]);
        $counts['received'] = (int) $stmt2->fetchColumn();
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}

echo json_encode(['status' => 'success'] + $counts);
