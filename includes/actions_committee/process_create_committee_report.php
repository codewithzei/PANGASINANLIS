<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/committee_report.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please sign in again.']);
    exit;
}

$service = new CommitteeReport($pdo);
$result = $service->createFromPost($_POST, (int) $userId);

echo json_encode($result);
exit;
