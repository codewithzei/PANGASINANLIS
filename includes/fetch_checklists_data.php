<?php
require_once 'db.php';

function getAllChecklists() {
    global $pdo;
    
    try {
        // Fetch requirements with their linked document types (grouped)
        $query = "
            SELECT 
                r.requirement_id,
                r.requirement_name,
                r.status,
                r.created_at,
                r.updated_at,
                GROUP_CONCAT(dt.document_type_name ORDER BY dt.document_type_name SEPARATOR ', ') AS document_types,
                GROUP_CONCAT(dr.document_type_id ORDER BY dt.document_type_name SEPARATOR ',') AS document_type_ids
            FROM requirements r
            LEFT JOIN document_requirement dr ON r.requirement_id = dr.requirement_id
            LEFT JOIN document_types dt ON dr.document_type_id = dt.document_type_id
            WHERE r.is_deleted = FALSE
            GROUP BY r.requirement_id, r.requirement_name, r.status, r.created_at, r.updated_at
            ORDER BY r.requirement_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getRequirementById($requirementId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                r.requirement_id,
                r.requirement_name,
                r.status,
                r.is_deleted,
                r.created_at,
                r.updated_at,
                GROUP_CONCAT(dr.document_type_id ORDER BY dr.document_type_id SEPARATOR ',') AS document_type_ids
            FROM requirements r
            LEFT JOIN document_requirement dr ON r.requirement_id = dr.requirement_id
            WHERE r.requirement_id = :id 
              AND r.is_deleted = FALSE
            GROUP BY r.requirement_id, r.requirement_name, r.status, r.is_deleted, r.created_at, r.updated_at
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $requirementId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getRequirement' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $requirementId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $requirement = getRequirementById($requirementId);
    
    if ($requirement) {
        echo json_encode([
            'status' => 'success',
            'data' => $requirement
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Requirement not found or deleted'
        ]);
    }
    exit;
}
?>
