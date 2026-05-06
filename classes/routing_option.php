<?php
class RoutingOption {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function createRoutingOption($routingOptionName, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang created_by sa INSERT query
            $sql = "INSERT INTO routing_options (routing_option_name, status, is_deleted, created_by) 
                    VALUES (:name, 'active', 0, :user_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $routingOptionName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Routing option added successfully!"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Routing option already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateRoutingOption($routingOptionId, $routingOptionName, $status, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE routing_options 
                    SET routing_option_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE routing_option_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $routingOptionId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $routingOptionName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Routing option updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made."];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Routing option name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteRoutingOption($routingOptionId, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Dahil Soft Delete ito (UPDATE talaga siya), i-re-record din natin
            // kung sino ang nag-delete sa pamamagitan ng pag-update sa updated_by.
            $sql = "UPDATE routing_options 
                    SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE routing_option_id = :id 
                    AND is_deleted = FALSE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $routingOptionId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Routing option deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "Routing option not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>