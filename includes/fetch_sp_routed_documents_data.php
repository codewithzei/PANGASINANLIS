<?php
require_once 'db.php';

// =========================================================================
// 1. FUNCTION PARA SA DATA TABLE (May Tab Filtering & User ID Filter)
// =========================================================================
function getAllRoutedDocuments($userId, $tab = 'ongoing') {
    global $pdo;
    
    try {
        // Base query gamit ang LEFT JOIN
        // MAGIC FIX: Kukunin lang niya yung mga papel na HINAWAKAN at IPINASA ng naka-login na user
        $query = "
            SELECT 
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                dt.document_type_name,
                ds.document_status_name AS status_name,
                ro.routing_option_name AS division_name,
                d.created_at
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            LEFT JOIN routing_options ro ON d.current_routing_option_id = ro.routing_option_id
            WHERE EXISTS (
                SELECT 1 FROM document_routes dr 
                WHERE dr.document_id = d.document_id 
                AND dr.routed_by_user_id = :user_id
                AND dr.action_taken IN ('ROUTED', 'FORWARDED')
            )
        ";

        // Filtering based sa napiling tab
        if ($tab === 'ongoing') {
            $query .= " AND ds.document_status_name IN (
                'Pending', 'Under Processing', 'Referred', 'Remanded', 
                'Returned to Plenary', 'For Committee Report', 'Lay on the Table', 
                'Deferred', 'For Opinion', 'For Calendar', 'On Going'
            )";
        } elseif ($tab === 'completed') {
            $query .= " AND ds.document_status_name IN ('Approved', 'Noted', 'Completed')";
        } elseif ($tab === 'withdrawn') {
            $query .= " AND ds.document_status_name IN ('Withdrawn')";
        }

        // I-sort mula sa pinakabago
        $query .= " ORDER BY d.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 2. FUNCTION PARA SA VIEW MODAL (Full Details + Attachments)
// =========================================================================
function getRoutedDocumentById($docId) {
    global $pdo;
    
    try {
        // I-jo-join natin lahat ng related tables para kumpleto ang detalye pag v-in-iew
        $query = "
            SELECT 
                d.*,
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
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docId, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // KUNG MAY NAKUHA, KUNIN DIN NATIN ANG ATTACHMENTS AT I-FORMAT ANG SOURCE
        if ($doc) {
            // 1. Kunin ang attachments para sa modal list
            $stmtAttach = $pdo->prepare("SELECT file_name, file_path FROM document_attachments WHERE document_id = :id");
            $stmtAttach->execute([':id' => $docId]);
            $doc['attachments'] = $stmtAttach->fetchAll(PDO::FETCH_ASSOC);

            // 2. I-format ang Source Name
            $mainSource = !empty($doc['source_name']) ? $doc['source_name'] : ($doc['source_type_name'] ?? 'N/A');
            $location = '';
            
            if (!empty($doc['muni_city_name'])) {
                $location = ' (' . $doc['muni_city_name'] . ')';
            } elseif (!empty($doc['hospital_name'])) {
                $location = ' - ' . $doc['hospital_name'];
            } elseif (!empty($doc['external_office_name'])) {
                if (empty($doc['source_name'])) {
                    $mainSource = $doc['external_office_name'];
                } else {
                    $location = ' - ' . $doc['external_office_name'];
                }
            }
            
            // Ipapasok natin sa bagong array key na 'formatted_source'
            $doc['formatted_source'] = $mainSource . $location;
        }

        return $doc;
        
    } catch (PDOException $e) {
        return null;
    }
}

// =========================================================================
// 3. AJAX HANDLER (Ito yung tinatawag ng JavaScript mo kapag nag-click ng View)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'getRoutedDocument' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $doc = getRoutedDocumentById($docId);
    
    if ($doc) {
        echo json_encode([
            'status' => 'success',
            'data' => $doc
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document not found'
        ]);
    }
    exit;
}
?>