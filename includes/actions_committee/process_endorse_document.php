<?php
session_start();

// I-adjust na lang ulit itong mga path depende sa folder structure mo.
require_once '../../includes/db.php'; 
require_once '../../classes/document.php';

header('Content-Type: application/json');

// 1. Siguraduhin na POST request ito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Kunin at i-sanitize ang mga inputs
    $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
    // Sanitize textarea input
    $instructions = trim(filter_input(INPUT_POST, 'instructions', FILTER_SANITIZE_SPECIAL_CHARS));
    
    // Dahil array ang checkbox mula sa form (`name="office_ids[]"`), ganito ang tamang pag-check:
    $officeIds = isset($_POST['office_ids']) && is_array($_POST['office_ids']) ? $_POST['office_ids'] : [];
    
    // 3. Kunin ang User ID ng naka-login based sa login structure mo
    $userId = $_SESSION['user_id'] ?? null;

    // --- BASIC VALIDATIONS ---
    
    // Kung walang ID o walang naka-login, i-block agad.
    if (!$documentId || !$userId) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Unauthorized action or missing document ID.'
        ]);
        exit;
    }

    // Kung nakalimutan mag-check ng opisina
    if (empty($officeIds)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Please select at least one opinion office.'
        ]);
        exit;
    }

    // 4. Tawagin na natin yung endorsement function sa Document Class
    try {
        $docObj = new Document($pdo);
        
        // I-execute ang action
        $result = $docObj->endorseToOffices($documentId, $officeIds, $instructions, $userId);

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$isAjax) {
            $_SESSION['endorse_status'] = $result['status'];
            $_SESSION['endorse_message'] = $result['message'];
        }

        // I-bato pabalik sa frontend ang JSON response
        echo json_encode($result);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'System error: ' . $e->getMessage()
        ]);
        exit;
    }

} else {
    // Kapag direktang in-access yung link sa browser (GET request)
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method.'
    ]);
    exit;
}
?>