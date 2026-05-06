<?php
class Role {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function createRole($roleName, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang created_by sa INSERT query
            $sql = "INSERT INTO user_roles (user_role_name, status, is_deleted, created_by) 
                    VALUES (:name, 1, 0, :user_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $roleName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Role added successfully!"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Role already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateRole($roleId, $roleName, $status, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE user_roles 
                    SET user_role_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE user_role_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $roleName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Role updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made."];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Role name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteRole($roleId, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Dahil Soft Delete ito (UPDATE talaga siya), i-re-record din natin
            // kung sino ang nag-delete sa pamamagitan ng pag-update sa updated_by.
            $sql = "UPDATE user_roles 
                    SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE user_role_id = :id 
                    AND is_deleted = FALSE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Role deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "Role not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>