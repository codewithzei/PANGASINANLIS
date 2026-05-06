<?php
class Document {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 1. ANG AUTOMATIC TRACKING NUMBER GENERATOR
    public function generateTrackingNumber() {
        $currentYear = date('Y');
        
        $sql = "SELECT tracking_number FROM documents 
                WHERE tracking_number LIKE :yearPattern 
                ORDER BY document_id DESC LIMIT 1";
                
        $stmt = $this->pdo->prepare($sql);
        // Tinanggal na natin yung brackets sa pag-search
        $stmt->execute([':yearPattern' => "$currentYear-%"]); 
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['tracking_number'])) {
            // Diretso na explode kasi wala ng brackets ("2026-00001")
            $parts = explode('-', $result['tracking_number']); 
            $newNumber = (int) $parts[1] + 1;  
            $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        } else {
            $formattedNumber = '00001';
        }

        // Tinanggal na rin natin yung brackets sa return value
        return "$currentYear-$formattedNumber"; 
    }

    // 2. ANG "CREATE INITIAL DOCUMENT" FUNCTION
    // 2. ANG "CREATE INITIAL DOCUMENT" FUNCTION
    public function createDocument($data, $requirements, $uploadedFiles, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Generate Tracking Number
            $trackingNumber = $this->generateTrackingNumber();

            // ========================================================
            // BUSINESS LOGIC: Check kung "Noted" ang pinili.
            // Alam natin na Noted ito kung may ipinasang communication category.
            // ========================================================
            $commCategory = !empty($data['communicationCategory']) ? $data['communicationCategory'] : null;

            // Kapag Noted (may category), si Admin ($userId) ang owner. Kapag hindi, in-transit (NULL).
            $ownerId = $commCategory ? $userId : null;

            // ========================================================
            // 2. INSERT SA MAIN `documents` TABLE
            // FIX: Idinagdag ang `communication_category_id` at `current_owner_user_id` logic
            // ========================================================
            $sqlDoc = "INSERT INTO documents 
                (tracking_number, date_received, subject_matter, document_type_id, 
                source_type_id, source_name, source_external_office_id, source_hospital_id, muni_city_id, 
                current_routing_option_id, communication_category_id, current_owner_user_id, version, status, created_by) 
                VALUES 
                (:tracking, :date_rec, :subject, :doc_type, 
                :source_type, :source_name, :ext_office, :hospital, :muni, 
                :routing_option, :comm_cat, :owner_id, 1, 1, :user_id)";
            
            $stmtDoc = $this->pdo->prepare($sqlDoc);
            $stmtDoc->execute([
                ':tracking'       => $trackingNumber,
                ':date_rec'       => $data['date_received'],
                ':subject'        => $data['subject_matter'],
                ':doc_type'       => $data['document_type_id'],
                ':source_type'    => $data['source_type_id'],
                ':source_name'    => $data['source_name'] ?: null,
                ':ext_office'     => $data['source_external_office_id'] ?: null,
                ':hospital'       => $data['source_hospital_id'] ?: null,
                ':muni'           => $data['muni_city_id'] ?: null,
                ':routing_option' => $data['routing_option_id'], 
                ':comm_cat'       => $commCategory, // Papasok dito kung anong pinili niya sa category
                ':owner_id'       => $ownerId,      // Papasok dito kung $userId ba o NULL
                ':user_id'        => $userId
            ]);

            $newDocId = $this->pdo->lastInsertId();

            // 3. INSERT SA `document_routes` (Initial Routing)
            $sqlRoute = "INSERT INTO document_routes 
                (document_id, routed_by_user_id, routed_to_option_id, action_taken, remarks) 
                VALUES (:doc_id, :user_id, :target_option, 'CREATED', :remarks)";
            
            $stmtRoute = $this->pdo->prepare($sqlRoute);
            $stmtRoute->execute([
                ':doc_id'        => $newDocId,
                ':user_id'       => $userId,
                ':target_option' => $data['routing_option_id'],
                ':remarks'       => $data['remarks'] ?: 'Initial document submission'
            ]);

            // 4. INSERT REQUIREMENTS
            if (!empty($requirements)) {
                $sqlReq = "INSERT INTO document_submitted_requirements (document_id, requirement_id) VALUES (:doc_id, :req_id)";
                $stmtReq = $this->pdo->prepare($sqlReq);
                foreach ($requirements as $reqId) {
                    $stmtReq->execute([':doc_id' => $newDocId, ':req_id' => $reqId]);
                }
            }

            // 5. INSERT ATTACHMENTS
            if (!empty($uploadedFiles)) {
                $sqlFile = "INSERT INTO document_attachments (document_id, file_name, file_path, uploaded_by) 
                            VALUES (:doc_id, :fname, :fpath, :user_id)";
                $stmtFile = $this->pdo->prepare($sqlFile);
                foreach ($uploadedFiles as $file) {
                    $stmtFile->execute([
                        ':doc_id'  => $newDocId,
                        ':fname'   => $file['name'],
                        ':fpath'   => $file['path'],
                        ':user_id' => $userId
                    ]);
                }
            }

            // 6. INSERT SA `document_history` 
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, 'CREATED', 'Document initially created and status set to Pending.')";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id'  => $newDocId,
                ':user_id' => $userId
            ]);

            // 7. INSERT SA `audit_logs`
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; 
            $logType = 'ROUTING'; 

            $auditDetails = "Created new document. Tracking No: $trackingNumber";
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'CREATE', 'DCMT_RTNG', :details, :log_type, :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id'    => $userId,
                ':details'    => $auditDetails,
                ':log_type'   => $logType,
                ':ip_address' => $ipAddress
            ]);

            // COMMIT LAHAT KAPAG SUCCESSFUL
            $this->pdo->commit();
            return ['status' => 'success', 'message' => "Document successfully routed! Tracking Number: $trackingNumber"];

        } catch (PDOException $e) {
            // I-rollback lahat kapag may nag-error kahit isa
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Failed to create document: ' . $e->getMessage()];
        }
    }

    // =========================================================================
    // FUNCTION PARA I-RECEIVE ANG DOCUMENT MULA SA INBOX
    // =========================================================================
    // Nagdagdag tayo ng $roleName parameter (may default value just in case)
    public function receiveDocument($documentId, $userId, $roleName = 'Assigned Officer') {
        try {
            $this->pdo->beginTransaction();

            // 1. UPDATE SA `documents` TABLE
            $sqlUpdateDoc = "UPDATE documents 
                             SET current_owner_user_id = :user_id,
                                 updated_at = CURRENT_TIMESTAMP
                             WHERE document_id = :doc_id 
                               AND current_owner_user_id IS NULL"; 
            
            $stmtDoc = $this->pdo->prepare($sqlUpdateDoc);
            $stmtDoc->execute([
                ':user_id' => $userId,
                ':doc_id'  => $documentId
            ]);

            if ($stmtDoc->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['status' => 'error', 'message' => 'Document may have already been received by another user or does not exist.'];
            }

            // Kunin ang Tracking Number
            $stmtTrack = $this->pdo->prepare("SELECT tracking_number FROM documents WHERE document_id = :doc_id");
            $stmtTrack->execute([':doc_id' => $documentId]);
            $trackingNumber = $stmtTrack->fetchColumn();

            // 2. RECORD SA `document_routes`
            $sqlRoute = "INSERT INTO document_routes 
                         (document_id, routed_by_user_id, action_taken, remarks) 
                         VALUES (:doc_id, :user_id, 'RECEIVED', :remarks)";
            $stmtRoute = $this->pdo->prepare($sqlRoute);
            $stmtRoute->execute([
                ':doc_id'  => $documentId,
                ':user_id' => $userId,
                ':remarks' => "Document officially received by $roleName." // DYNAMIC REMARKS NA
            ]);

            // 3. RECORD SA `document_history`
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, 'RECEIVED', :remarks)";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id'  => $documentId,
                ':user_id' => $userId,
                ':remarks' => "Document was claimed and officially received by $roleName." // DYNAMIC REMARKS NA
            ]);

            // 4. RECORD SA `audit_logs`
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $auditDetails = "Received document ($trackingNumber) as $roleName.";
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'RECEIVE', 'DCMT_INBOX', :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id'    => $userId,
                ':details'    => $auditDetails,
                ':ip_address' => $ipAddress
            ]);

            $this->pdo->commit();
            
            return [
                'status' => 'success', 
                'message' => "Document $trackingNumber received successfully!"
            ];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Failed to receive document: ' . $e->getMessage()];
        }
    }

    // =========================================================================
    // UNIVERSAL FUNCTION PARA I-ROUTE ANG DOCUMENT SA SUSUNOD NA OPISINA
    // =========================================================================
    public function routeDocument($documentId, $trackingNumber, $forwardToId, $remarks, $uploadedFiles, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. DEFAULT REMARKS
            if (empty($remarks)) {
                $remarks = "Document routed to the Plenary division for appropriate action.";
            }

            $actionWord = 'ROUTED';

            // 2. UPDATE SA MAIN `documents` TABLE
            // Tinatanggal ang ownership at inililipat ang routing option destination
            $sqlUpdateDoc = "UPDATE documents 
                             SET current_routing_option_id = :forward_to,
                                 current_owner_user_id = NULL,
                                 updated_at = CURRENT_TIMESTAMP
                             WHERE document_id = :doc_id";
            $stmtDoc = $this->pdo->prepare($sqlUpdateDoc);
            $stmtDoc->execute([
                ':forward_to' => $forwardToId,
                ':doc_id'     => $documentId
            ]);
            
            $auditAction = "Routed document ($trackingNumber)";

            // 3. RECORD SA `document_routes`
            $sqlRoute = "INSERT INTO document_routes (document_id, routed_by_user_id, routed_to_option_id, action_taken, remarks) 
                         VALUES (:doc_id, :user_id, :target_option, :action_taken, :remarks)";
            $stmtRoute = $this->pdo->prepare($sqlRoute);
            $stmtRoute->execute([
                ':doc_id'        => $documentId,
                ':user_id'       => $userId,
                ':target_option' => $forwardToId,
                ':action_taken'  => $actionWord,
                ':remarks'       => $remarks
            ]);

            // 4. RECORD SA `document_history`
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, :action, :remarks)";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id'  => $documentId,
                ':user_id' => $userId,
                ':action'  => $actionWord,
                ':remarks' => $remarks
            ]);

            // 5. INSERT ATTACHMENTS KUNG MERON
            if (!empty($uploadedFiles)) {
                $sqlFile = "INSERT INTO document_attachments (document_id, file_name, file_path, uploaded_by) 
                            VALUES (:doc_id, :fname, :fpath, :user_id)";
                $stmtFile = $this->pdo->prepare($sqlFile);
                foreach ($uploadedFiles as $file) {
                    $stmtFile->execute([
                        ':doc_id'  => $documentId,
                        ':fname'   => $file['name'],
                        ':fpath'   => $file['path'],
                        ':user_id' => $userId
                    ]);
                }
            }

            // 6. RECORD SA `audit_logs`
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'ROUTE', 'DCMT_PROC', :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id'    => $userId,
                ':details'    => $auditAction,
                ':ip_address' => $ipAddress
            ]);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => "Document $trackingNumber successfully routed!"];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Failed to route document: ' . $e->getMessage()];
        }
    }
}
?>