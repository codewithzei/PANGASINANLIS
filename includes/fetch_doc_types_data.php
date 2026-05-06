<?php
require_once 'db.php';

function getAllDocTypes() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                document_type_id,
                document_type_name,
                status,
                created_at,
                updated_at
            FROM document_types
            WHERE is_deleted = FALSE
            ORDER BY document_type_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getDocTypeById($docTypeId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                document_type_id,
                document_type_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM document_types
            WHERE document_type_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docTypeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getDocType' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docTypeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $docType = getDocTypeById($docTypeId);
    
    if ($docType) {
        echo json_encode([
            'status' => 'success',
            'data' => $docType
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document type not found or deleted'
        ]);
    }
    exit;
}
?>
