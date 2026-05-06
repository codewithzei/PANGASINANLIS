<?php
require_once 'db.php';

function getAllMuniCities() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                muni_city_id,
                muni_city_name,
                status,
                created_at,
                updated_at
            FROM muni_cities
            WHERE is_deleted = FALSE
            ORDER BY muni_city_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getMuniCityById($muniCityId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                muni_city_id,
                muni_city_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM muni_cities
            WHERE muni_city_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $muniCityId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getMuniCity' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $muniCityId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $muniCity = getMuniCityById($muniCityId);
    
    if ($muniCity) {
        echo json_encode([
            'status' => 'success',
            'data' => $muniCity
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Municipality/City not found or deleted'
        ]);
    }
    exit;
}
?>
