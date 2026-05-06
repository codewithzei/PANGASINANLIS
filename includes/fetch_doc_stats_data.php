<?php
require_once 'db.php';

function getAllDocStats() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                document_status_id,
                document_status_name,
                status,
                created_at,
                updated_at
            FROM document_statuses
            WHERE is_deleted = FALSE
            ORDER BY document_status_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getDocStatById($docStatId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                document_status_id,
                document_status_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM document_statuses
            WHERE document_status_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docStatId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getDocStat' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docStatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $docStat = getDocStatById($docStatId);
    
    if ($docStat) {
        echo json_encode([
            'status' => 'success',
            'data' => $docStat
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document status not found or deleted'
        ]);
    }
    exit;
}
?>
