<?php
class Requirement {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // BAGO: Dinagdag ang $userId bilang pangatlong parameter
    public function createRequirement($requirementName, $docTypeIds, $userId) {
        try {
            // Simulan ang transaction
            $this->pdo->beginTransaction();

            // ========================================================
            // STEP 1: I-check kung existing na ang Requirement Name
            // ========================================================
            $checkSql = "SELECT requirement_id FROM requirements WHERE requirement_name = :name LIMIT 1";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([':name' => trim($requirementName)]);
            $existingReq = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingReq) {
                // KUNG EXISTING NA: Gamitin yung luma niyang ID
                $requirementId = $existingReq['requirement_id'];
                $message = "Existing requirement found. New document types linked successfully!";
            } else {
                // KUNG WALA PA: Gawa tayo ng bagong record
                $sql = "INSERT INTO requirements (requirement_name, status, is_deleted, created_by) 
                        VALUES (:name, 1, 0, :user_id)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':name' => trim($requirementName),
                    ':user_id' => $userId
                ]);
                $requirementId = $this->pdo->lastInsertId();
                $message = "New requirement and links added successfully!";
            }

            // ========================================================
            // STEP 2: Filter at Insert sa Pivot Table (document_requirement)
            // ========================================================
            if (!empty($docTypeIds) && is_array($docTypeIds)) {
                
                // 2A. Kunin muna lahat ng Document Type IDs na naka-link na sa Requirement na 'to
                $existingLinksSql = "SELECT document_type_id FROM document_requirement WHERE requirement_id = :req_id";
                $existingLinksStmt = $this->pdo->prepare($existingLinksSql);
                $existingLinksStmt->execute([':req_id' => $requirementId]);
                
                // Gagawin nitong simple array ang result: e.g., [1, 2]
                $existingDocIds = $existingLinksStmt->fetchAll(PDO::FETCH_COLUMN);

                // 2B. I-filter ang mga pinili ng user. 
                // array_diff = Kunin lang sa $docTypeIds yung MGA WALA PA sa $existingDocIds
                $newDocIdsToInsert = array_diff($docTypeIds, $existingDocIds);

                // 2C. I-insert lang yung mga BAGO at hindi pa naka-link
                if (!empty($newDocIdsToInsert)) {
                    $pivotSql = "INSERT INTO document_requirement (document_type_id, requirement_id) 
                                 VALUES (:doc_id, :req_id)";
                    $pivotStmt = $this->pdo->prepare($pivotSql);

                    foreach ($newDocIdsToInsert as $docId) {
                        $pivotStmt->execute([
                            ':doc_id' => $docId,
                            ':req_id' => $requirementId
                        ]);
                    }
                } else if ($existingReq) {
                    // Kung existing na yung requirement tapos WALA rin tayong nai-add na bagong doc type
                    $message = "Requirement already exists and is already linked to the selected document types. No changes made.";
                }
            }

            // I-commit ang changes
            $this->pdo->commit();
            
            return ["status" => "success", "message" => $message];

        } catch (PDOException $e) {
            // Kapag may error, bawiin lahat
            $this->pdo->rollBack();

            // Optional: Kung paano nag-fail ang DB sa unexpected scenarios
            return ["status" => "error", "message" => "Database Error: " . $e->getMessage()];
        }
    }
    
    // BAGO: Dinagdag ang $userId bilang pang-limang parameter
    public function updateRequirement($requirementId, $requirementName, $status, $docTypeIds, $userId) {
        try {
            // Simulan ang transaction
            $this->pdo->beginTransaction();

            // 1. Update sa requirements table
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE requirements 
                    SET requirement_name = :name, 
                        status = :status,
                        updated_by = :user_id, 
                        updated_at = NOW() 
                    WHERE requirement_id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $requirementId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $requirementName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            $stmt->execute();

            // 2. I-delete muna lahat ng existing links sa document_requirement para sa requirement na ito
            $deleteSql = "DELETE FROM document_requirement WHERE requirement_id = :req_id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute([':req_id' => $requirementId]);

            // 3. I-insert ulit ang mga bagong links sa document_requirement (Pivot Table)
            if (!empty($docTypeIds) && is_array($docTypeIds)) {
                $pivotSql = "INSERT INTO document_requirement (document_type_id, requirement_id) 
                             VALUES (:doc_id, :req_id)";
                $pivotStmt = $this->pdo->prepare($pivotSql);

                foreach ($docTypeIds as $docId) {
                    $pivotStmt->execute([
                        ':doc_id' => $docId,
                        ':req_id' => $requirementId
                    ]);
                }
            }

            // Kung umabot dito nang walang error, i-commit ang changes
            $this->pdo->commit();
            
            return ["status" => "success", "message" => "Requirement and links updated successfully!"];

        } catch (PDOException $e) {
            // Kapag may error, bawiin lahat ng ginawa (Rollback)
            $this->pdo->rollBack();

            if ($e->getCode() == 23000) {
                return ["status" => "error", "message" => "Requirement name already exists!"];
            }
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // BAGO: Dinagdag ang $userId bilang pangalawang parameter
    public function deleteRequirement($requirementId, $userId) {
        try {
            // BAGO: Isinama ang updated_by sa SET clause
            $sql = "UPDATE requirements 
                    SET is_deleted = TRUE, 
                        updated_by = :user_id,
                        updated_at = NOW() 
                    WHERE requirement_id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $requirementId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // BAGO: I-bind ang user ID
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["status" => "success", "message" => "Requirement deleted successfully!"];
            } else {
                // BAGO: Dinagdagan ko lang ng "or already deleted" para mas clear ang error
                return ["status" => "error", "message" => "Requirement not found or already deleted."]; 
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
}
?>