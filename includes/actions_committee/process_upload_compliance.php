<?php
session_start();
require_once '../db.php'; 
require_once __DIR__ . '/../../classes/Document.php'; // Siguraduhin ang capitalization kung sensitive ang server mo

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
    $endorsementId = filter_input(INPUT_POST, 'endorsement_id', FILTER_VALIDATE_INT);
    
    // Kukunin natin pero hindi na natin haharangin kung empty
    $description = trim(filter_input(INPUT_POST, 'compliance_description', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
    
    $userId = $_SESSION['user_id'] ?? null;
    $fileData = $_FILES['compliance_file'] ?? null;

    if (!$documentId || !$endorsementId || !$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized action or missing IDs.']);
        exit;
    }
    
    // File na lang ang i-re-require natin na hindi pwedeng wala
    if (!$fileData || $fileData['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Please upload a valid compliance file.']);
        exit;
    }

    $docObj = new Document($pdo);
    // Ipasa kahit empty string yung $description, sasaluhin na 'yan ng system
    $result = $docObj->uploadCompliance($endorsementId, $documentId, $description, $fileData, $userId);

    echo json_encode($result);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>