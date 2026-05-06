<?php
class ExternalOffice
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function createExternalOffice($externalOfficeName, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang created_by sa INSERT query
            $sql = "INSERT INTO external_offices (external_office_name, status, is_deleted, created_by) 
                    VALUES (:name, 'active', 0, :user_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $externalOfficeName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "External office added successfully!"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "External office already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function updateExternalOffice($externalOfficeId, $externalOfficeName, $status, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE external_offices 
                    SET external_office_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE external_office_id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $externalOfficeId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $externalOfficeName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "External office updated successfully!"];
                } else {
                    return ["status" => "error", "message" => "No changes were made."];
                }
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "External office name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function deleteExternalOffice($externalOfficeId, $userId) { // BAGO: Dinagdag ang $userId
        try {
            // BAGO: Dahil Soft Delete ito (UPDATE talaga siya), i-re-record din natin
            // kung sino ang nag-delete sa pamamagitan ng pag-update sa updated_by.
            $sql = "UPDATE external_offices 
                    SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE external_office_id = :id 
                    AND is_deleted = FALSE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $externalOfficeId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return ["status" => "success", "message" => "External office deleted successfully!"];
                } else {
                    return ["status" => "error", "message" => "External office not found or already deleted."];
                }
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
?>