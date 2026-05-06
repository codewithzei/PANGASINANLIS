<?php
require_once 'db.php';

function getAllExternalOffices() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                external_office_id,
                external_office_name,
                status,
                created_at,
                updated_at
            FROM external_offices
            WHERE is_deleted = FALSE
            ORDER BY external_office_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getExternalOfficeById($externalOfficeId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                external_office_id,
                external_office_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM external_offices
            WHERE external_office_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $externalOfficeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getExternalOffice' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $externalOfficeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $externalOffice = getExternalOfficeById($externalOfficeId);
    
    if ($externalOffice) {
        echo json_encode([
            'status' => 'success',
            'data' => $externalOffice
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'External office not found or deleted'
        ]);
    }
    exit;
}
?>
