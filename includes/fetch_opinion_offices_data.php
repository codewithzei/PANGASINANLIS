<?php
require_once 'db.php';

function getAllOpinionOffices() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                opinion_office_id,
                opinion_office_code,
                opinion_office_name,
                status,
                created_at,
                updated_at
            FROM opinion_offices
            WHERE is_deleted = FALSE
            ORDER BY opinion_office_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getOpinionOfficeById($opinionOfficeId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                opinion_office_id,
                opinion_office_code,
                opinion_office_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM opinion_offices
            WHERE opinion_office_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $opinionOfficeId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

// AJAX Handler para sa Edit/View fetching via JavaScript
if (isset($_GET['action']) && $_GET['action'] === 'getOpinionOffice' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $opinionOfficeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $opinionOffice = getOpinionOfficeById($opinionOfficeId);
    
    if ($opinionOffice) {
        echo json_encode([
            'status' => 'success',
            'data' => $opinionOffice
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Opinion office not found or deleted'
        ]);
    }
    exit;
}
?>