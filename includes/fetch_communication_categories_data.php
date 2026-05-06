<?php
require_once 'db.php';

function getAllCommunicationCategories() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                communication_category_id,
                communication_category_name,
                status,
                created_at,
                updated_at
            FROM communication_categories
            WHERE is_deleted = FALSE
            ORDER BY communication_category_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getCommunicationCategoryById($communicationCategoryId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                communication_category_id,
                communication_category_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM communication_categories
            WHERE communication_category_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $communicationCategoryId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

// Handle AJAX requests
if (isset($_GET['action']) && $_GET['action'] === 'getCommunicationCategory' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $communicationCategoryId = intval($_GET['id']);
    $communicationCategory = getCommunicationCategoryById($communicationCategoryId);
    
    if ($communicationCategory) {
        echo json_encode([
            'status' => 'success',
            'data' => $communicationCategory
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Communication category not found'
        ]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getCommunicationCategory' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $communicationCategoryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $communicationCategory = getCommunicationCategoryById($communicationCategoryId);
    
    if ($communicationCategory) {
        echo json_encode([
            'status' => 'success',
            'data' => $communicationCategory
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Communication category not found or deleted'
        ]);
    }
    exit;
}
?>
