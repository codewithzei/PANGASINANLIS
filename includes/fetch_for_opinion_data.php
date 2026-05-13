<?php
// Kailangan natin ng session_start() dito just in case tawagin as AJAX handler sa future
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; // I-adjust kung iba ang path sa database mo

// =========================================================================
// 1. FUNCTION PARA SA "FOR OPINION" DATA (May kasamang Nested Table data)
// =========================================================================
function getForOpinionDocuments($userId) {
    global $pdo;
    
    try {
        // 1. Kunin ang main documents na hawak mo at 'For Opinion' ang status
        $query = "
            SELECT 
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                d.version AS cycle,
                ds.document_status_name AS status_name,
                d.created_at
            FROM documents d
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            WHERE d.current_owner_user_id = :user_id 
              AND ds.document_status_name = 'For Opinion'
            ORDER BY d.created_at DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. I-loop ang documents para kunin ang mga in-endorse na offices (LATEST ROUND ONLY)
        foreach ($documents as &$doc) {
            $endQuery = "
                SELECT 
                    de.endorsement_id,
                    de.endorsement_round,
                    oo.opinion_office_name,
                    os.opinion_status_id,
                    os.opinion_status_name,
                    de.opinion_remarks,
                    de.compliance_description
                FROM document_endorsements de
                
                /* DITO YUNG MAGIC: Kukunin lang natin yung may pinakamataas na round per office */
                INNER JOIN (
                    SELECT opinion_office_id, MAX(endorsement_round) as max_round
                    FROM document_endorsements
                    WHERE document_id = :doc_id
                    GROUP BY opinion_office_id
                ) latest_round 
                    ON de.opinion_office_id = latest_round.opinion_office_id 
                    AND de.endorsement_round = latest_round.max_round

                LEFT JOIN opinion_offices oo ON de.opinion_office_id = oo.opinion_office_id
                LEFT JOIN opinion_statuses os ON de.opinion_status_id = os.opinion_status_id
                WHERE de.document_id = :doc_id
                ORDER BY oo.opinion_office_name ASC
            ";
            
            $endStmt = $pdo->prepare($endQuery);
            $endStmt->execute([':doc_id' => $doc['document_id']]);
            $endorsements = $endStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 3. Bilangin ang progress bar ("0 / 1 Favorable") at i-format ang text
            $favorableCount = 0;
            $totalOffices = count($endorsements);
            
            foreach ($endorsements as &$office) {
                if ($office['opinion_status_id'] == 2) { // Favorable
                    $favorableCount++; 
                }
                
                // I-format ang "Latest Action" para ididisplay na lang sa HTML table
                if ($office['opinion_status_id'] == 1) {
                    $office['latest_action'] = "Awaiting initial opinion";
                } elseif ($office['opinion_status_id'] == 2) {
                    $office['latest_action'] = !empty($office['opinion_remarks']) ? "Favorable: " . $office['opinion_remarks'] : "Opinion submitted (Favorable)";
                } elseif ($office['opinion_status_id'] == 3) {
                    $office['latest_action'] = !empty($office['opinion_remarks']) ? "Unfavorable: " . $office['opinion_remarks'] : "Opinion submitted (Unfavorable)";
                } elseif ($office['opinion_status_id'] == 4) {
                    $office['latest_action'] = !empty($office['compliance_description']) ? "Compliance submitted: " . $office['compliance_description'] : "Compliance submitted. Awaiting 2nd opinion.";
                }
            }
            
            // Resolve: lahat ng opisina settled (Favorable o final Unfavorable) + may kahit isang final Unfavorable
            $needsResolve = false;
            if ($totalOffices > 0) {
                $allSettled = true;
                $anyFinalUnfavorable = false;
                foreach ($endorsements as $off) {
                    $sid = (int) ($off['opinion_status_id'] ?? 0);
                    $round = (int) ($off['endorsement_round'] ?? 1);
                    if ($sid === 1 || $sid === 4) {
                        $allSettled = false;
                        break;
                    }
                    if ($sid === 2) {
                        continue;
                    }
                    if ($sid === 3) {
                        $isFinal = $round >= 2 && !empty($off['compliance_description']);
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
                $needsResolve = $allSettled && $anyFinalUnfavorable;
            }

            // I-attach ang processed data pabalik sa object
            $doc['endorsements'] = $endorsements;
            $doc['total_offices'] = $totalOffices;
            $doc['favorable_count'] = $favorableCount;
            $doc['progress_percentage'] = $totalOffices > 0 ? ($favorableCount / $totalOffices) * 100 : 0;
            $doc['needs_resolve'] = $needsResolve;
        }
        
        return $documents;
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 1b. READY FOR AGENDA (For Calendar)
// =========================================================================
function getReadyForAgendaDocuments($userId) {
    global $pdo;

    try {
        $query = "
            SELECT 
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                dt.document_type_name,
                st.source_type_name,
                d.source_name,
                eo.external_office_name,
                h.hospital_name,
                mc.muni_city_name,
                ds.document_status_name AS status_name, /* <--- IDINAGDAG DITO */
                d.version AS cycle,
                COALESCE(
                    (
                        SELECT created_at 
                        FROM document_routes 
                        WHERE document_id = d.document_id 
                          AND routed_to_option_id = d.current_routing_option_id
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ), 
                    d.created_at
                ) AS date_referred
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            LEFT JOIN source_types st ON d.source_type_id = st.source_type_id
            LEFT JOIN external_offices eo ON d.source_external_office_id = eo.external_office_id
            LEFT JOIN hospitals h ON d.source_hospital_id = h.hospital_id
            LEFT JOIN muni_cities mc ON d.muni_city_id = mc.muni_city_id
            WHERE d.current_owner_user_id = :user_id 
              AND ds.document_status_name = 'For Calendar'
            ORDER BY d.updated_at DESC, d.created_at DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($documents as &$doc) {
            $sourceVal = '';
            if (!empty($doc['muni_city_name'])) {
                $sourceVal = $doc['muni_city_name'];
            } elseif (!empty($doc['hospital_name'])) {
                $sourceVal = $doc['hospital_name'];
            } elseif (!empty($doc['external_office_name'])) {
                $sourceVal = $doc['external_office_name'];
            } elseif (!empty($doc['source_name'])) {
                $sourceVal = $doc['source_name'];
            }
            $sourceType = $doc['source_type_name'] ?? 'Unknown Source';
            $doc['source_display'] = !empty($sourceVal) ? $sourceType . ' - ' . $sourceVal : $sourceType;
        }

        return $documents;
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 2. FUNCTION PARA SA VIEW MODAL (Full Details ng For Opinion Document)
// =========================================================================
function getForOpinionDocumentById($docId, $userId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                d.*,
                d.version AS cycle,
                dt.document_type_name,
                st.source_type_name,
                eo.external_office_name,
                h.hospital_name,
                mc.muni_city_name,
                ro.routing_option_name AS current_division,
                cc.communication_category_name,
                ds.document_status_name AS status_name
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN source_types st ON d.source_type_id = st.source_type_id
            LEFT JOIN external_offices eo ON d.source_external_office_id = eo.external_office_id
            LEFT JOIN hospitals h ON d.source_hospital_id = h.hospital_id
            LEFT JOIN muni_cities mc ON d.muni_city_id = mc.muni_city_id
            LEFT JOIN routing_options ro ON d.current_routing_option_id = ro.routing_option_id
            LEFT JOIN communication_categories cc ON d.communication_category_id = cc.communication_category_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            WHERE d.document_id = :id 
              AND d.current_owner_user_id = :user_id 
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            // Kunin ang attachments ng main document
            $stmtAtt = $pdo->prepare("SELECT file_name, file_path FROM document_attachments WHERE document_id = :doc_id");
            $stmtAtt->execute([':doc_id' => $docId]);
            $doc['attachments'] = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);

            // Kunin din natin yung endorsements history para sa modal kung kailangan mong i-display
            $stmtEnd = $pdo->prepare("
                SELECT de.*, oo.opinion_office_name, os.opinion_status_name 
                FROM document_endorsements de
                LEFT JOIN opinion_offices oo ON de.opinion_office_id = oo.opinion_office_id
                LEFT JOIN opinion_statuses os ON de.opinion_status_id = os.opinion_status_id
                WHERE de.document_id = :doc_id
                ORDER BY oo.opinion_office_name ASC, de.endorsement_round ASC
            ");
            $stmtEnd->execute([':doc_id' => $docId]);
            $doc['endorsements'] = $stmtEnd->fetchAll(PDO::FETCH_ASSOC);
        }

        return $doc;
        
    } catch (PDOException $e) {
        return null;
    }
}

// =========================================================================
// 3. AJAX HANDLER (Para sa View Details Button sa For Opinion Documents)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'getForOpinionDocument' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    $doc = getForOpinionDocumentById($docId, $userId);
    
    if ($doc) {
        echo json_encode([
            'status' => 'success',
            'data' => $doc
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document not found or it is not assigned to you.'
        ]);
    }
    exit;
}
?>