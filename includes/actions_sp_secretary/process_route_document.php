<?php
session_start();
// PAKI-ADJUST ANG PATHS KUNG KINAKAILANGAN
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/document.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    // KUNIN ANG DATA MULA SA FORM
    $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
    $trackingNumber = trim($_POST['tracking_number'] ?? '');
    
    // Pinalitan natin yung name base sa HTML form mo (routingOption)
    $forwardToId = filter_input(INPUT_POST, 'routingOption', FILTER_VALIDATE_INT); 
    $remarks = trim($_POST['remarks'] ?? '');

    // Validation: Siguraduhing may document ID at napiling papuntahan
    if (!$documentId || !$forwardToId) {
        $_SESSION['route_status'] = 'error';
        $_SESSION['route_message'] = 'Missing required document information or routing destination.';
        header('Location: /PangasinanLIS/pages/sp_secretary/received_documents');
        exit;
    }

    // =======================================================
    // I-HANDLE ANG FILE UPLOADS
    // =======================================================
    $uploadedFiles = [];
    $uploadDirectory = __DIR__ . '/../../../assets/documents/'; 
    
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $fileCount = count($_FILES['attachments']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            $originalName = $_FILES['attachments']['name'][$i];
            $tmpName      = $_FILES['attachments']['tmp_name'][$i];
            
            $newFileName = time() . '_' . basename($originalName);
            $destination = $uploadDirectory . $newFileName;
            $dbFilePath  = 'assets/documents/' . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFiles[] = [
                    'name' => $originalName,
                    'path' => $dbFilePath
                ];
            }
        }
    }

    // =======================================================
    // TAWAGIN ANG FUNCTION SA DOCUMENT CLASS!
    // =======================================================
    $docObj = new Document($pdo);
    
    // Ginamit na natin yung bago nating function (Wala nang actionType)
    $result = $docObj->routeDocument(
        $documentId, 
        $trackingNumber, 
        $forwardToId, 
        $remarks, 
        $uploadedFiles, 
        $userId
    );

    // I-SET ANG SESSION STATUS AT I-REDIRECT
    $_SESSION['route_status'] = $result['status'];
    $_SESSION['route_message'] = $result['message'];
    header('Location: /PangasinanLIS/pages/sp_secretary/received_documents'); 
    exit;
}
?>