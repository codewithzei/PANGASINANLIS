<?php
session_start();

// I-adjust mo na lang itong mga path depende sa kung saang folder mo i-sa-save itong file na 'to.
require_once '../../includes/db.php'; 
require_once '../../classes/document.php';

header('Content-Type: application/json');

// 1. Siguraduhin na POST request ito at may tamang action na ipinasa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'receiveDocument') {
    
    // 2. Kunin at i-sanitize yung Document ID
    $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);
    
    // 3. Kunin ang User ID at Role ng naka-login based sa login.php mo.
    $userId = $_SESSION['user_id'] ?? null;
    
    // Kukunin niya yung role_name mula sa session mo (Automatic itong magiging "Committee" kung Committee ang naka-login)
    $roleName = $_SESSION['role_name'] ?? 'Assigned Officer'; 

    // Kung walang ID o walang naka-login, i-block agad.
    if (!$documentId || !$userId) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Unauthorized action or missing document ID.'
        ]);
        exit;
    }

    // 4. Tawagin na natin yung magic function natin sa Document Class
    try {
        $docObj = new Document($pdo);
        
        // ITO YUNG BAGO PARA SA COMMITTEE: Pinalitan natin yung module name para sa Audit Logs
        $result = $docObj->receiveDocument($documentId, $userId, $roleName, 'DCMT_COMMITTEE_INBOX');

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // 5. Set session flash only for non-AJAX page loads to avoid duplicate messages
        if (!$isAjax) {
            $_SESSION['receive_status'] = $result['status'];
            $_SESSION['receive_message'] = $result['message'];
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