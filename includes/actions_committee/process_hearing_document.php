<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/Document.php'; // Siguraduhin ang capitalization kung sensitive ang server mo

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
$targetStatusId = filter_input(INPUT_POST, 'document_status_id', FILTER_VALIDATE_INT);

// TINANGGAL: Yung pagkuha ng remarks galing sa POST

if (!$userId || !$documentId || !$targetStatusId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized action or missing required fields.']);
    exit;
}

$docObj = new Document($pdo);
// TINANGGAL: Yung pagpasa ng $remarks sa function dahil 3 arguments na lang ang hinihingi natin
$result = $docObj->completeHearingOutcome($documentId, (int) $userId, (int) $targetStatusId);

echo json_encode($result);
exit;