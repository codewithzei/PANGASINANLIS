<?php
require_once 'db.php';

function getAllOpinionStatuses() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                opinion_status_id,
                opinion_status_name,
                status,
                created_at,
                updated_at
            FROM opinion_statuses
            WHERE is_deleted = FALSE
            ORDER BY opinion_status_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getOpinionStatusById($opinionStatusId1) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                opinion_status_id,
                opinion_status_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM opinion_statuses
            WHERE opinion_status_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $opinionStatusId1, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getOpinionStatus' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $opinionStatusId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $opinionStatus = getOpinionStatusById($opinionStatusId);
    
    if ($opinionStatus) {
        echo json_encode([
            'status' => 'success',
            'data' => $opinionStatus
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Opinion status not found or deleted'
        ]);
    }
    exit;
}
?>
