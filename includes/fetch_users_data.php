<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../classes/user.php';

function getAllUsersData() {
    global $pdo;
    
    // Check if $pdo is set
    if (!isset($pdo)) {
        return ["error" => "Database connection error."];
    }
    
    try {
        $user = new User($pdo);
        $result = $user->getAllUsers();
        
        if ($result['status'] === 'success') {
            return $result['data'];
        } else {
            return ["error" => $result['message']];
        }
    } catch (Exception $e) {
        // Handle unexpected exceptions to ensure $docStats loop doesn't fail
        return ["error" => "An unexpected error occurred: " . $e->getMessage()];
    }
}
?>
