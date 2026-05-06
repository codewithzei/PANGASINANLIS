<?php
require_once 'db.php';

// =========================================================================
// 1. FUNCTION PARA SA INBOX DATA TABLE
// =========================================================================
function getSPSecretaryInbox() {
    global $pdo;
    
    try {
        // NOTE: I-palit mo ang '2' sa kung ano man ang totoong ID ng "SP Secretary"
        $spSecRoutingId = 1; 

        $query = "
            SELECT 
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                dt.document_type_name,
                ds.document_status_name AS status_name,
                d.created_at
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            WHERE d.current_routing_option_id = :routing_id 
              AND d.current_owner_user_id IS NULL -- HINDI PA NAKE-CLAIM
            ORDER BY d.created_at ASC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':routing_id' => $spSecRoutingId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 2. FUNCTION PARA SA VIEW MODAL NG INBOX (Full Details)
// =========================================================================
function getInboxDocumentById($docId) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                d.*,
                dt.document_type_name,
                st.source_type_name,
                eo.external_office_name,
                h.hospital_name,
                mc.muni_city_name,
                ro.routing_option_name AS current_division,
                ds.document_status_name AS status_name,
                dr.remarks
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN source_types st ON d.source_type_id = st.source_type_id
            LEFT JOIN external_offices eo ON d.source_external_office_id = eo.external_office_id
            LEFT JOIN hospitals h ON d.source_hospital_id = h.hospital_id
            LEFT JOIN muni_cities mc ON d.muni_city_id = mc.muni_city_id
            LEFT JOIN routing_options ro ON d.current_routing_option_id = ro.routing_option_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            LEFT JOIN document_routes dr
                ON dr.document_id = d.document_id
                AND dr.route_id = (
                    SELECT MAX(route_id) FROM document_routes
                    WHERE document_id = d.document_id
                      AND action_taken = 'CREATED'
                )
            WHERE d.document_id = :id 
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $docId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return null;
    }
}

// =========================================================================
// 3. AJAX HANDLER (Ito yung tinatawag ng JavaScript mo kapag nag-click ng View)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'getInboxDocument' && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $docId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $doc = getInboxDocumentById($docId);
    
    if ($doc) {
        echo json_encode([
            'status' => 'success',
            'data' => $doc
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Document not found or already received.'
        ]);
    }
    exit;
}
?>