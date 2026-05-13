<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Siguraduhing na-include ang db.php sa page kung saan mo tatawagin ito.
// Kung sakaling magka-error na 'require_once', tanggalin mo na lang ito kung na-declare na sa main file.
require_once __DIR__ . '/db.php'; 

// =========================================================================
// 1. HELPER FUNCTION: Reusable query para hindi humaba ang code
// =========================================================================
function fetchHearingsByStatus($userId, $statusFilter = null) {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                a.agenda_id,
                a.agenda_number,
                a.agenda_date,
                a.chairperson,
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                d.version AS cycle,
                ds.document_status_name AS status_name
            FROM documents d
            INNER JOIN agendas a ON d.document_id = a.document_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            WHERE d.current_owner_user_id = :user_id 
        ";

        // Kung may specific status na hinahanap (ex: 'Approved')
        if ($statusFilter !== null) {
            $query .= " AND ds.document_status_name = :status_name ";
        }

        $query .= " ORDER BY a.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        if ($statusFilter !== null) {
            $stmt->bindParam(':status_name', $statusFilter, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $hearings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format dates
        foreach ($hearings as &$hearing) {
            if (!empty($hearing['agenda_date'])) {
                $hearing['agenda_date'] = date('M d, Y', strtotime($hearing['agenda_date']));
            } else {
                $hearing['agenda_date'] = 'N/A';
            }
        }
        
        return $hearings;
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// =========================================================================
// 2. TAB FUNCTIONS (Para sa bawat listahan sa Dashboard)
// =========================================================================

// ALL HEARINGS
function getAllHearings($userId) {
    return fetchHearingsByStatus($userId, null);
}

// SCHEDULED (Ito yung naging 'On Going' nung sinchedule natin)
function getScheduledHearings($userId) {
    return fetchHearingsByStatus($userId, 'On Going');
}

// APPROVED
function getApprovedHearings($userId) {
    return fetchHearingsByStatus($userId, 'Approved');
}

// DEFERRED
function getDeferredHearings($userId) {
    return fetchHearingsByStatus($userId, 'Deferred');
}

// REMANDED
function getRemandedHearings($userId) {
    return fetchHearingsByStatus($userId, 'Remanded');
}

// WITHDRAWN
function getWithdrawnHearings($userId) {
    return fetchHearingsByStatus($userId, 'Withdrawn');
}

// =========================================================================
// 3. VIEW DOCUMENT FUNCTION (Para sa Full Details kasama Agenda)
// =========================================================================
function getHearingDocumentById($docId, $userId) {
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
                ds.document_status_name AS status_name,
                a.agenda_number, 
                a.agenda_type, 
                a.chairperson, 
                a.agenda_date, 
                a.agenda_time, 
                a.venue, 
                a.notes AS agenda_notes
            FROM documents d
            INNER JOIN agendas a ON d.document_id = a.document_id
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
            ORDER BY a.created_at DESC 
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
?>