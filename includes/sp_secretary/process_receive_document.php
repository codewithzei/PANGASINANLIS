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
    
    // ITO YUNG BAGO BOSS: Kukunin niya yung role_name mula sa session mo
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
        
        // BAGO: Nagdagdag tayo ng 'DCMT_SPSEC_INBOX' para sa Audit Logs
        $result = $docObj->receiveDocument($documentId, $userId, $roleName, 'DCMT_SPSEC_INBOX');

        // 5. Kung success, mag-set ng session flash message para sa Inbox UI
        if ($result['status'] === 'success') {
            $_SESSION['receive_status'] = 'success';
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