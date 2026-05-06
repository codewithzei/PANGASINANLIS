<?php
require_once 'db.php';

function getAllHospitals() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                hospital_id,
                hospital_name,
                status,
                created_at,
                updated_at
            FROM hospitals
            WHERE is_deleted = FALSE
            ORDER BY hospital_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getHospitalById($hospitalId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                hospital_id,
                hospital_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM hospitals
            WHERE hospital_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $hospitalId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getHospital' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $hospitalId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $hospital = getHospitalById($hospitalId);
    
    if ($hospital) {
        echo json_encode([
            'status' => 'success',
            'data' => $hospital
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hospital not found or deleted'
        ]);
    }
    exit;
}
?>
