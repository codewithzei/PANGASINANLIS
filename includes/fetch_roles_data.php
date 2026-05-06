<?php
require_once 'db.php';

function getAllRoles() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                user_role_id,
                user_role_name,
                status,
                created_at,
                updated_at
            FROM user_roles
            WHERE is_deleted = FALSE
            ORDER BY user_role_id ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

function getRoleById($roleId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                user_role_id,
                user_role_name,
                status,
                is_deleted,
                created_at,
                updated_at
            FROM user_roles
            WHERE user_role_id = :id 
              AND is_deleted = FALSE
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getRole' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $roleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $role = getRoleById($roleId);
    
    if ($role) {
        echo json_encode([
            'status' => 'success',
            'data' => $role
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Role not found or deleted'
        ]);
    }
    exit;
}
?>
