<?php
require_once 'db.php';

function getAllRoutingOptions() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                routing_option_id,
                routing_option_name,
                status,
                created_at,
                updated_at
            FROM routing_options
            WHERE is_deleted = FALSE
            ORDER BY routing_option_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getRoutingOptionById($routingOptionId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                routing_option_id,
                routing_option_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM routing_options
            WHERE routing_option_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $routingOptionId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getRoutingOption' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $routingOptionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $routingOption = getRoutingOptionById($routingOptionId);
    
    if ($routingOption) {
        echo json_encode([
            'status' => 'success',
            'data' => $routingOption
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Routing option not found or deleted'
        ]);
    }
    exit;
}
?>
