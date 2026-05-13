<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/document.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
$resolution = trim((string) ($_POST['resolution'] ?? ''));

if (!$userId || !$documentId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized action or missing document.']);
    exit;
}

$docObj = new Document($pdo);

if ($resolution === 'withdraw') {
    $result = $docObj->resolveWithdrawFromOpinion($documentId, (int) $userId);
} elseif ($resolution === 'proceed') {
    $result = $docObj->resolveProceedToCalendar($documentId, (int) $userId);
} else {
    $result = ['status' => 'error', 'message' => 'Invalid resolution.'];
}

echo json_encode($result);
exit;
