<?php
// Kailangan natin ng session_start() dito para sa AJAX handler sa baba
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// =========================================================================
// 1. FUNCTION PARA SA REFERRED TAB LIST (Dashboard)
// =========================================================================
function getReferredDocuments($userId) {
    global $pdo;
    
    try {
        // Gumamit tayo ng COALESCE at Sub-Query para kunin sa document_routes
        // kung kailan siya eksaktong pinasa sa routing_option (division) mo.
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
                ds.document_status_name AS status_name,
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
            AND ds.document_status_name = 'Referred' /* <--- FIXED: Exact match para walang ibang sisingit */
            ORDER BY date_referred DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // FORMATTING SA PHP PARA MADALI NA LANG I-DISPLAY SA UI TABLE (JavaScript)
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

            // Pagsasamahin natin yung Type at Value (ex: "Client - Zyron Manangan" o "Hospital - Asingan Hospital")
            $sourceType = $doc['source_type_name'] ?? 'Unknown Source';
            $doc['source_display'] = !empty($sourceVal) ? $sourceType . ' - ' . $sourceVal : $sourceType;
        }
        
        return $documents;
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 2. FUNCTION PARA SA VIEW MODAL (Full Details ng hawak mong document)
// =========================================================================
function getReferredDocumentById($docId, $userId) {
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
              AND ds.document_status_name = 'Referred' /* <--- FIXED: Exact match din dito */
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kung may nakuha tayong document, kunin din natin yung attachments niya
        if ($doc) {
            $stmtAtt = $pdo->prepare("SELECT file_name, file_path FROM document_attachments WHERE document_id = :doc_id");
            $stmtAtt->execute([':doc_id' => $docId]);
            $doc['attachments'] = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $doc;
        
    } catch (PDOException $e) {
        return null;
    }
}

// =========================================================================
// 3. AJAX HANDLER (Para sa View Details Button sa Referred Documents)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'getReferredDocument' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    $doc = getReferredDocumentById($docId, $userId);
    
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