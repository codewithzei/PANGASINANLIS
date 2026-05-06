<?php
class CommunicationCategory
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function createCommunicationCategory($communicationCategoryName, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang created_by sa INSERT query
            $sql = "INSERT INTO communication_categories (communication_category_name, status, is_deleted, created_by) 
                    VALUES (:name, 1, 0, :user_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $communicationCategoryName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Communication category added successfully!"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Communication category already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateCommunicationCategory($communicationCategoryId, $communicationCategoryName, $status, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE communication_categories 
                    SET communication_category_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE communication_category_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $communicationCategoryId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $communicationCategoryName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Communication category updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made."];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Communication category name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteCommunicationCategory($communicationCategoryId, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Dahil Soft Delete ito (UPDATE talaga siya), i-re-record din natin
            // kung sino ang nag-delete sa pamamagitan ng pag-update sa updated_by.
            $sql = "UPDATE communication_categories SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE communication_category_id = :id 
                    AND is_deleted = FALSE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $communicationCategoryId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "Communication category deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "Communication category not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>