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
    public function receiveDocument($documentId, $userId, $roleName = 'Assigned Officer', $moduleName = 'DCMT_INBOX') {
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
            
            // Mapapansin mo dito, :module_name na ang ginamit natin
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'RECEIVE', :module_name, :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id'     => $userId,
                ':module_name' => $moduleName, // Ipapasa niya yung 4th parameter dito
                ':details'     => $auditDetails,
                ':ip_address'  => $ipAddress
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

    // =========================================================================
    // FUNCTION PARA I-ENDORSE ANG DOCUMENT SA MGA OFFICES (FOR OPINION)
    // =========================================================================
    public function endorseToOffices($documentId, $officeIds, $instructions, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. UPDATE SA `documents` TABLE
            // Ibahin ang status to 'For Opinion' (Base sa dump mo, ang For Opinion ay ID 12)
            $sqlUpdateDoc = "UPDATE documents 
                             SET status = 12, 
                                 updated_at = CURRENT_TIMESTAMP 
                             WHERE document_id = :doc_id";
            $stmtDoc = $this->pdo->prepare($sqlUpdateDoc);
            $stmtDoc->execute([':doc_id' => $documentId]);

            // 2. INSERT SA `document_endorsements`
            // Gagawa ito ng row para sa BAWAT office na chineck sa checkbox
            $sqlEndorse = "INSERT INTO document_endorsements 
                           (document_id, opinion_office_id, endorsement_round, opinion_status_id, endorsement_remarks, created_by) 
                           VALUES (:doc_id, :office_id, 1, 1, :remarks, :user_id)";
            $stmtEndorse = $this->pdo->prepare($sqlEndorse);

            foreach ($officeIds as $officeId) {
                $stmtEndorse->execute([
                    ':doc_id'    => $documentId,
                    ':office_id' => $officeId,
                    ':remarks'   => !empty($instructions) ? $instructions : null,
                    ':user_id'   => $userId
                ]);
            }

            // 3. RECORD SA `document_history`
            $officeCount = count($officeIds);
            $histRemarks = "Document endorsed to $officeCount office(s) for opinion.";
            
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, 'ENDORSED', :remarks)";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id'  => $documentId,
                ':user_id' => $userId,
                ':remarks' => $histRemarks
            ]);

            // 4. RECORD SA `audit_logs`
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'ENDORSE', 'DCMT_ENDORSE', :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id'    => $userId,
                ':details'    => "Endorsed Document ID: $documentId to $officeCount office(s).",
                ':ip_address' => $ipAddress
            ]);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => "Document successfully endorsed to selected offices!"];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => 'Failed to endorse document: ' . $e->getMessage()];
        }
    }

    // =========================================================================
    // FUNCTION PARA MAG-UPLOAD NG OPINION (Favorable / Unfavorable)
    // =========================================================================
    public function uploadOpinion($endorsementId, $documentId, $statusId, $remarks, $fileData, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Handle File Upload
            $fileName = null;
            if ($fileData && $fileData['error'] === UPLOAD_ERR_OK) {
                // Gumawa ng unique filename para hindi mag-overwrite
                $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                $fileName = 'opinion_' . $documentId . '_' . time() . '.' . $fileExtension;
                
                // Siguraduhing may 'uploads/opinions' folder ka
                $uploadDir = '../../uploads/opinions/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $destination = $uploadDir . $fileName;
                if (!move_uploaded_file($fileData['tmp_name'], $destination)) {
                    throw new Exception("Failed to save the uploaded file to the server.");
                }
            } else {
                throw new Exception("No valid file uploaded.");
            }

            // 2. Update `document_endorsements`
            $sql = "UPDATE document_endorsements 
                    SET opinion_status_id = :status_id, 
                        opinion_remarks = :remarks, 
                        opinion_attachment = :file_name,
                        updated_by = :user_id 
                    WHERE endorsement_id = :endorsement_id"; // <--- DITO ANG BINAGO (Ginawang endorsement_id)
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':status_id' => $statusId,
                ':remarks' => !empty($remarks) ? $remarks : null,
                ':file_name' => $fileName,
                ':user_id' => $userId,
                ':endorsement_id' => $endorsementId
            ]);

            // 3. Record History
            $statusWord = ($statusId == 2) ? 'Favorable' : 'Unfavorable';
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, 'OPINION_UPLOADED', :remarks)";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id' => $documentId,
                ':user_id' => $userId,
                ':remarks' => "A $statusWord opinion was uploaded."
            ]);

            // 4. Audit Log
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'UPLOAD', 'DCMT_OPINION', :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id' => $userId,
                ':details' => "Uploaded $statusWord opinion for Endorsement ID: $endorsementId",
                ':ip_address' => $ipAddress
            ]);

            $this->syncDocumentStatusAfterEndorsementChange($documentId, $userId);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => "Opinion successfully uploaded!"];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // =========================================================================
    // DOCUMENT STATUS HELPERS (Opinion → Calendar / Resolve)
    // =========================================================================
    public function getDocumentStatusIdByName($name) {
        $stmt = $this->pdo->prepare("SELECT document_status_id FROM document_statuses WHERE document_status_name = :n AND COALESCE(is_deleted, 0) = 0 LIMIT 1");
        $stmt->execute([':n' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['document_status_id'] : null;
    }

    /**
     * Latest endorsement row per opinion_office_id → list of opinion_status_id.
     */
    private function fetchLatestRoundOpinionStatusIds($documentId) {
        $sql = "SELECT de.opinion_status_id
                FROM document_endorsements de
                INNER JOIN (
                    SELECT opinion_office_id, MAX(endorsement_round) AS max_round
                    FROM document_endorsements
                    WHERE document_id = :doc_id
                    GROUP BY opinion_office_id
                ) lr ON de.opinion_office_id = lr.opinion_office_id AND de.endorsement_round = lr.max_round
                WHERE de.document_id = :doc_id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':doc_id' => $documentId, ':doc_id2' => $documentId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function isAllFavorableLatest($statusIds) {
        if (empty($statusIds)) {
            return false;
        }
        foreach ($statusIds as $sid) {
            if ($sid !== 2) {
                return false;
            }
        }
        return true;
    }

    /**
     * Latest row per office: opinion_status_id, endorsement_round, compliance_description.
     */
    private function fetchLatestRoundEndorsementRowsForResolve($documentId) {
        $sql = "SELECT de.opinion_status_id, de.endorsement_round, de.compliance_description
                FROM document_endorsements de
                INNER JOIN (
                    SELECT opinion_office_id, MAX(endorsement_round) AS max_round
                    FROM document_endorsements
                    WHERE document_id = :doc_id
                    GROUP BY opinion_office_id
                ) lr ON de.opinion_office_id = lr.opinion_office_id AND de.endorsement_round = lr.max_round
                WHERE de.document_id = :doc_id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':doc_id' => $documentId, ':doc_id2' => $documentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * True when every office's latest row is settled (Favorable, or final Unfavorable)
     * and at least one office is final Unfavorable (round >= 2 with compliance on latest row).
     * Matches needs_resolve in fetch_for_opinion_data.php.
     */
    public function isMixedTerminalWithUnfavorable($documentId) {
        $rows = $this->fetchLatestRoundEndorsementRowsForResolve($documentId);
        if (empty($rows)) {
            return false;
        }
        $allSettled = true;
        $anyFinalUnfavorable = false;
        foreach ($rows as $row) {
            $sid = (int) ($row['opinion_status_id'] ?? 0);
            $round = (int) ($row['endorsement_round'] ?? 1);
            if ($sid === 1 || $sid === 4) {
                $allSettled = false;
                break;
            }
            if ($sid === 2) {
                continue;
            }
            if ($sid === 3) {
                $isFinal = $round >= 2 && !empty($row['compliance_description']);
                if ($isFinal) {
                    $anyFinalUnfavorable = true;
                } else {
                    $allSettled = false;
                    break;
                }
                continue;
            }
            $allSettled = false;
            break;
        }
        return $allSettled && $anyFinalUnfavorable;
    }

    /**
     * After opinion/compliance change: if document is still For Opinion and every latest office is Favorable → For Calendar.
     */
    public function syncDocumentStatusAfterEndorsementChange($documentId, $userId) {
        $stmt = $this->pdo->prepare("SELECT ds.document_status_name FROM documents d INNER JOIN document_statuses ds ON d.status = ds.document_status_id WHERE d.document_id = :id LIMIT 1");
        $stmt->execute([':id' => $documentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || $row['document_status_name'] !== 'For Opinion') {
            return;
        }

        $latest = $this->fetchLatestRoundOpinionStatusIds($documentId);
        if (!$this->isAllFavorableLatest($latest)) {
            return;
        }

        $forCalendarId = $this->getDocumentStatusIdByName('For Calendar');
        $forOpinionId = $this->getDocumentStatusIdByName('For Opinion');
        if ($forCalendarId === null || $forOpinionId === null) {
            return;
        }

        $upd = $this->pdo->prepare("UPDATE documents SET status = :new_st, updated_at = CURRENT_TIMESTAMP WHERE document_id = :id AND status = :old_st");
        $upd->execute([':new_st' => $forCalendarId, ':id' => $documentId, ':old_st' => $forOpinionId]);
        if ($upd->rowCount() < 1) {
            return;
        }

        $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) VALUES (:doc_id, :user_id, 'STATUS_UPDATE', :remarks)";
        $stmtHist = $this->pdo->prepare($sqlHist);
        $stmtHist->execute([
            ':doc_id' => $documentId,
            ':user_id' => $userId,
            ':remarks' => 'All required opinions are favorable. Document marked For Calendar.',
        ]);

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) VALUES (:user_id, 'UPDATE', 'DCMT_STATUS', :details, 'SUCCESS', :ip_address)";
        $stmtAudit = $this->pdo->prepare($sqlAudit);
        $stmtAudit->execute([
            ':user_id' => $userId,
            ':details' => "Document ID $documentId set to For Calendar (all opinions favorable).",
            ':ip_address' => $ipAddress,
        ]);
    }

    /**
     * Resolve: proceed despite mixed opinions — mark latest unfavorable rows skipped, set For Calendar.
     */
    public function resolveProceedToCalendar($documentId, $userId) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT d.current_owner_user_id, ds.document_status_name FROM documents d INNER JOIN document_statuses ds ON d.status = ds.document_status_id WHERE d.document_id = :id LIMIT 1");
            $stmt->execute([':id' => $documentId]);
            $docRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$docRow || (int) $docRow['current_owner_user_id'] !== (int) $userId) {
                throw new Exception('Document not found or not assigned to you.');
            }
            if ($docRow['document_status_name'] !== 'For Opinion') {
                throw new Exception('Document is not in For Opinion status.');
            }
            if (!$this->isMixedTerminalWithUnfavorable($documentId)) {
                throw new Exception('This document does not require a mixed-opinion resolution.');
            }

            $sqlIds = "SELECT de.endorsement_id
                       FROM document_endorsements de
                       INNER JOIN (
                           SELECT opinion_office_id, MAX(endorsement_round) AS max_round
                           FROM document_endorsements
                           WHERE document_id = :doc_id
                           GROUP BY opinion_office_id
                       ) lr ON de.opinion_office_id = lr.opinion_office_id AND de.endorsement_round = lr.max_round
                       WHERE de.document_id = :doc_id2 AND de.opinion_status_id = 3";
            $stmtIds = $this->pdo->prepare($sqlIds);
            $stmtIds->execute([':doc_id' => $documentId, ':doc_id2' => $documentId]);
            $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

            $updSkip = $this->pdo->prepare("UPDATE document_endorsements SET is_skipped = 1, updated_by = :uid, updated_at = CURRENT_TIMESTAMP WHERE endorsement_id = :eid");
            foreach ($ids as $eid) {
                $updSkip->execute([':uid' => $userId, ':eid' => (int) $eid]);
            }

            $forCalendarId = $this->getDocumentStatusIdByName('For Calendar');
            $forOpinionId = $this->getDocumentStatusIdByName('For Opinion');
            if ($forCalendarId === null || $forOpinionId === null) {
                throw new Exception('Required document statuses are missing in the database (For Calendar / For Opinion).');
            }

            $upd = $this->pdo->prepare("UPDATE documents SET status = :new_st, updated_at = CURRENT_TIMESTAMP WHERE document_id = :id AND status = :old_st");
            $upd->execute([':new_st' => $forCalendarId, ':id' => $documentId, ':old_st' => $forOpinionId]);
            if ($upd->rowCount() < 1) {
                throw new Exception('Could not update document status.');
            }

            $stmtHist = $this->pdo->prepare("INSERT INTO document_history (document_id, user_id, action, remarks) VALUES (:doc_id, :user_id, 'RESOLVE_PROCEED', :remarks)");
            $stmtHist->execute([
                ':doc_id' => $documentId,
                ':user_id' => $userId,
                ':remarks' => 'Committee proceeded to calendar despite one or more unfavorable opinions (offices marked skipped).',
            ]);

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmtAudit = $this->pdo->prepare("INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) VALUES (:user_id, 'UPDATE', 'DCMT_RESOLVE', :details, 'SUCCESS', :ip_address)");
            $stmtAudit->execute([
                ':user_id' => $userId,
                ':details' => "Resolve proceed: Document ID $documentId → For Calendar.",
                ':ip_address' => $ipAddress,
            ]);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Document moved to For Calendar. You may now schedule the agenda.'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Resolve: withdraw document from opinion workflow.
     */
    public function resolveWithdrawFromOpinion($documentId, $userId) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT d.current_owner_user_id, ds.document_status_name FROM documents d INNER JOIN document_statuses ds ON d.status = ds.document_status_id WHERE d.document_id = :id LIMIT 1");
            $stmt->execute([':id' => $documentId]);
            $docRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$docRow || (int) $docRow['current_owner_user_id'] !== (int) $userId) {
                throw new Exception('Document not found or not assigned to you.');
            }
            if ($docRow['document_status_name'] !== 'For Opinion') {
                throw new Exception('Document is not in For Opinion status.');
            }
            if (!$this->isMixedTerminalWithUnfavorable($documentId)) {
                throw new Exception('This document does not require a mixed-opinion resolution.');
            }

            $withdrawnId = $this->getDocumentStatusIdByName('Withdrawn');
            $forOpinionId = $this->getDocumentStatusIdByName('For Opinion');
            if ($withdrawnId === null || $forOpinionId === null) {
                throw new Exception('Required document statuses are missing in the database.');
            }

            $upd = $this->pdo->prepare("UPDATE documents SET status = :new_st, updated_at = CURRENT_TIMESTAMP WHERE document_id = :id AND status = :old_st");
            $upd->execute([':new_st' => $withdrawnId, ':id' => $documentId, ':old_st' => $forOpinionId]);
            if ($upd->rowCount() < 1) {
                throw new Exception('Could not update document status.');
            }

            $stmtHist = $this->pdo->prepare("INSERT INTO document_history (document_id, user_id, action, remarks) VALUES (:doc_id, :user_id, 'RESOLVE_WITHDRAW', :remarks)");
            $stmtHist->execute([
                ':doc_id' => $documentId,
                ':user_id' => $userId,
                ':remarks' => 'Document withdrawn after mixed opinion outcome.',
            ]);

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmtAudit = $this->pdo->prepare("INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) VALUES (:user_id, 'UPDATE', 'DCMT_RESOLVE', :details, 'SUCCESS', :ip_address)");
            $stmtAudit->execute([
                ':user_id' => $userId,
                ':details' => "Resolve withdraw: Document ID $documentId → Withdrawn.",
                ':ip_address' => $ipAddress,
            ]);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Document has been withdrawn.'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Insert agenda row; document must be For Calendar and owned by user.
     */
    public function scheduleAgenda($documentId, $userId, $data) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT d.current_owner_user_id, ds.document_status_name FROM documents d INNER JOIN document_statuses ds ON d.status = ds.document_status_id WHERE d.document_id = :id LIMIT 1");
            $stmt->execute([':id' => $documentId]);
            $docRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$docRow || (int) $docRow['current_owner_user_id'] !== (int) $userId) {
                throw new Exception('Document not found or not assigned to you.');
            }
            if ($docRow['document_status_name'] !== 'For Calendar' && $docRow['document_status_name'] !== 'Ready for Agenda') {
                throw new Exception('Document must be For Calendar/Ready for Agenda before scheduling an agenda.');
            }

            $committeeId = (int) ($data['committee_id'] ?? 0);
            $agendaNumber = trim($data['agenda_number'] ?? '');
            $agendaType = trim($data['agenda_type'] ?? '');
            $chairperson = trim($data['chairperson'] ?? '');
            $agendaDate = trim($data['agenda_date'] ?? '');
            $agendaTime = trim($data['agenda_time'] ?? '');
            $venue = trim($data['venue'] ?? ''); // BAGO: Sinalo ang venue
            $notes = trim($data['notes'] ?? '');

            // BAGO: Isinama sa validation ang $venue
            if ($committeeId < 1 || $agendaNumber === '' || $agendaType === '' || $chairperson === '' || $agendaDate === '' || $agendaTime === '' || $venue === '') {
                throw new Exception('Please fill in all required agenda fields including venue.');
            }

            $chk = $this->pdo->prepare("SELECT 1 FROM committees WHERE committee_id = :cid AND COALESCE(is_deleted, 0) = 0 LIMIT 1");
            $chk->execute([':cid' => $committeeId]);
            if (!$chk->fetchColumn()) {
                throw new Exception('Invalid committee selected.');
            }

            // BAGO: Isinama ang venue sa INSERT query
            $sql = "INSERT INTO agendas (document_id, committee_id, agenda_number, agenda_type, chairperson, agenda_date, agenda_time, venue, notes)
                    VALUES (:doc_id, :comm_id, :num, :type, :chair, :adate, :atime, :venue, :notes)";
            $ins = $this->pdo->prepare($sql);
            $ins->execute([
                ':doc_id' => $documentId,
                ':comm_id' => $committeeId,
                ':num' => $agendaNumber,
                ':type' => $agendaType,
                ':chair' => $chairperson,
                ':adate' => $agendaDate,
                ':atime' => $agendaTime,
                ':venue' => $venue, // BAGO
                ':notes' => $notes !== '' ? $notes : null,
            ]);

            // =========================================================================
            // BAGO: I-UPDATE ANG STATUS NG DOCUMENT TO "ON GOING"
            // =========================================================================
            $stmtGetOnGoingId = $this->pdo->prepare("SELECT document_status_id FROM document_statuses WHERE document_status_name = 'On Going' LIMIT 1");
            $stmtGetOnGoingId->execute();
            $onGoingId = $stmtGetOnGoingId->fetchColumn();

            if ($onGoingId) {
                // Update Main Document Status
                $stmtUpdateDoc = $this->pdo->prepare("UPDATE documents SET status = :new_status, updated_at = CURRENT_TIMESTAMP WHERE document_id = :doc_id");
                $stmtUpdateDoc->execute([':new_status' => $onGoingId, ':doc_id' => $documentId]);

                // Add History Log for progression
                $stmtProgHist = $this->pdo->prepare("INSERT INTO document_history (document_id, user_id, action, remarks) VALUES (:doc_id, :user_id, 'STATUS_UPDATED', 'Document scheduled for agenda. Status updated to On Going.')");
                $stmtProgHist->execute([':doc_id' => $documentId, ':user_id' => $userId]);
            }
            // =========================================================================

            // Original History Log para sa Agenda
            $stmtHist = $this->pdo->prepare("INSERT INTO document_history (document_id, user_id, action, remarks) VALUES (:doc_id, :user_id, 'AGENDA_SCHEDULED', :remarks)");
            $stmtHist->execute([
                ':doc_id' => $documentId,
                ':user_id' => $userId,
                ':remarks' => "Agenda scheduled: {$agendaNumber} ({$agendaType}) on {$agendaDate} {$agendaTime} at {$venue}.", // Added venue sa remarks
            ]);

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmtAudit = $this->pdo->prepare("INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) VALUES (:user_id, 'CREATE', 'DCMT_AGENDA', :details, 'SUCCESS', :ip_address)");
            $stmtAudit->execute([
                ':user_id' => $userId,
                ':details' => "Agenda created for Document ID $documentId.",
                ':ip_address' => $ipAddress,
            ]);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Agenda scheduled successfully. Document is now On Going.'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // =========================================================================
    // FUNCTION PARA MAG-UPLOAD NG COMPLIANCE (With "Final Unfavorable" Logic)
    // =========================================================================
    public function uploadCompliance($endorsementId, $documentId, $description, $fileData, $userId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Handle File Upload
            $fileName = null;
            if ($fileData && $fileData['error'] === UPLOAD_ERR_OK) {
                $fileExtension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                $fileName = 'compliance_' . $documentId . '_' . time() . '.' . $fileExtension;
                
                $uploadDir = '../../uploads/compliances/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $destination = $uploadDir . $fileName;
                if (!move_uploaded_file($fileData['tmp_name'], $destination)) {
                    throw new Exception("Failed to save the uploaded compliance file.");
                }
            } else {
                throw new Exception("No valid file uploaded.");
            }

            // ==========================================================
            // BAGO: Lagyan ng default value kung empty ang description
            // ==========================================================
            $description = trim($description);
            if (empty($description)) {
                $description = "Compliance requirements submitted. Attached file is ready for further evaluation.";
            }

            // 2. Kunin ang current round
            $stmtGet = $this->pdo->prepare("SELECT opinion_office_id, endorsement_round FROM document_endorsements WHERE endorsement_id = :id");
            $stmtGet->execute([':id' => $endorsementId]);
            $currentData = $stmtGet->fetch(PDO::FETCH_ASSOC);

            if (!$currentData) {
                throw new Exception("Endorsement record not found.");
            }

            $currentRound = (int) $currentData['endorsement_round'];

            // ==========================================================
            // LOGIC CHECK: Round 1 ba o Round 2 (Final)?
            // ==========================================================
            if ($currentRound >= 2) {
                
                // ROUND 2+ (DEAD END): I-save lang ang compliance file, HINDI na gagawa ng bagong round.
                // Status remains 3 (Unfavorable) para alam ng system na Final Unfavorable na ito.
                $sqlUpdate = "UPDATE document_endorsements 
                              SET compliance_description = :description, 
                                  compliance_attachment = :file_name,
                                  updated_by = :user_id 
                              WHERE endorsement_id = :endorsement_id";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':description' => $description, // Papasok dito yung default value kung empty
                    ':file_name' => $fileName,
                    ':user_id' => $userId,
                    ':endorsement_id' => $endorsementId
                ]);

                $histRemarks = "Final compliance submitted. Document marked as Final Unfavorable for this office.";

            } else {
                
                // ROUND 1: I-update to Status 4 at gumawa ng Round 2
                $sqlUpdate = "UPDATE document_endorsements 
                              SET opinion_status_id = 4, 
                                  compliance_description = :description, 
                                  compliance_attachment = :file_name,
                                  updated_by = :user_id 
                              WHERE endorsement_id = :endorsement_id";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':description' => $description, // Papasok dito yung default value kung empty
                    ':file_name' => $fileName,
                    ':user_id' => $userId,
                    ':endorsement_id' => $endorsementId
                ]);

                $nextRound = $currentRound + 1;
                $sqlInsert = "INSERT INTO document_endorsements 
                              (document_id, opinion_office_id, endorsement_round, opinion_status_id, created_by) 
                              VALUES (:doc_id, :office_id, :round, 4, :user_id)"; // 4 = For 2nd Endorsement
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':doc_id' => $documentId,
                    ':office_id' => $currentData['opinion_office_id'],
                    ':round' => $nextRound,
                    ':user_id' => $userId
                ]);

                $histRemarks = "Compliance submitted. Round $nextRound endorsement created.";
            }

            // 3. Record History & Audit
            $sqlHist = "INSERT INTO document_history (document_id, user_id, action, remarks) 
                        VALUES (:doc_id, :user_id, 'COMPLIANCE_UPLOADED', :remarks)";
            $stmtHist = $this->pdo->prepare($sqlHist);
            $stmtHist->execute([
                ':doc_id' => $documentId,
                ':user_id' => $userId,
                ':remarks' => $histRemarks
            ]);

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $sqlAudit = "INSERT INTO audit_logs (user_id, action, module, details, log_type, ip_address) 
                         VALUES (:user_id, 'UPLOAD', 'DCMT_COMPLIANCE', :details, 'SUCCESS', :ip_address)";
            $stmtAudit = $this->pdo->prepare($sqlAudit);
            $stmtAudit->execute([
                ':user_id' => $userId,
                ':details' => "Uploaded compliance for Endorsement ID: $endorsementId.",
                ':ip_address' => $ipAddress
            ]);

            $this->syncDocumentStatusAfterEndorsementChange($documentId, $userId);

            $this->pdo->commit();
            return ['status' => 'success', 'message' => "Compliance submitted successfully!"];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>