<?php
session_start();
require_once '../db.php'; 
require_once __DIR__ . '/../../classes/document.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
    $endorsementId = filter_input(INPUT_POST, 'endorsement_id', FILTER_VALIDATE_INT);
    $statusId = filter_input(INPUT_POST, 'opinion_status_id', FILTER_VALIDATE_INT);
    $remarks = trim(filter_input(INPUT_POST, 'opinion_remarks', FILTER_SANITIZE_SPECIAL_CHARS));
    
    $userId = $_SESSION['user_id'] ?? null;
    $fileData = $_FILES['opinion_file'] ?? null;

    if (!$documentId || !$endorsementId || !$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized action or missing IDs.']);
        exit;
    }

    if (!$statusId) {
        echo json_encode(['status' => 'error', 'message' => 'Please select an opinion type.']);
        exit;
    }

    if (!$fileData || $fileData['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Please upload a valid opinion file.']);
        exit;
    }

    $docObj = new Document($pdo);
    $result = $docObj->uploadOpinion($endorsementId, $documentId, $statusId, $remarks, $fileData, $userId);

    echo json_encode($result);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>