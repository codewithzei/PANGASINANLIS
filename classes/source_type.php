<?php
class SourceType {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function createSourceType($sourceTypeName, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang created_by sa INSERT query
            $sql = "INSERT INTO source_types (source_type_name, status, is_deleted, created_by) 
                    VALUES (:name, 'active', 0, :user_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $sourceTypeName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Source type added successfully!"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Source type already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateSourceType($sourceTypeId, $sourceTypeName, $status, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE source_types 
                    SET source_type_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE source_type_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $sourceTypeId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $sourceTypeName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Source type updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made."];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Source type name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteSourceType($sourceTypeId, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Dahil Soft Delete ito (UPDATE talaga siya), i-re-record din natin
            // kung sino ang nag-delete sa pamamagitan ng pag-update sa updated_by.
            $sql = "UPDATE source_types 
                    SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE source_type_id = :id 
                    AND is_deleted = FALSE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $sourceTypeId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Source type deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "Source type not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>