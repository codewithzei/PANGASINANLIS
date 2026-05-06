<?php
require_once 'db.php';

function getAllSourceTypes() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                source_type_id,
                source_type_name,
                status,
                created_at,
                updated_at
            FROM source_types
            WHERE is_deleted = FALSE
            ORDER BY source_type_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getSourceTypeById($sourceTypeId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                source_type_id,
                source_type_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM source_types
            WHERE source_type_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $sourceTypeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getSourceType' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $sourceTypeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $sourceType = getSourceTypeById($sourceTypeId);
    
    if ($sourceType) {
        echo json_encode([
            'status' => 'success',
            'data' => $sourceType
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Source type not found or deleted'
        ]);
    }
    exit;
}
?>
