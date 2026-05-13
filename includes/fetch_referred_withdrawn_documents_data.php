<?php
require_once __DIR__ . '/db.php';

/**
 * Committee "Referred Documents" — Withdrawn tab.
 * Lists documents with status Withdrawn at Committee (routing_option_id = 3)
 * and currently assigned to the logged-in user (same ownership rule as referred list).
 */
function getReferredWithdrawnDocuments($userId) {
    global $pdo;

    $committeeRoutingId = 3;

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
                ds.document_status_name AS status_name,
                d.version AS cycle,
                d.updated_at AS date_withdrawn
            FROM documents d
            LEFT JOIN document_types dt ON d.document_type_id = dt.document_type_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            LEFT JOIN source_types st ON d.source_type_id = st.source_type_id
            LEFT JOIN external_offices eo ON d.source_external_office_id = eo.external_office_id
            LEFT JOIN hospitals h ON d.source_hospital_id = h.hospital_id
            LEFT JOIN muni_cities mc ON d.muni_city_id = mc.muni_city_id
            WHERE d.current_owner_user_id = :user_id
              AND d.current_routing_option_id = :routing_id
              AND ds.document_status_name = 'Withdrawn'
            ORDER BY d.updated_at DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':user_id', (int) $userId, PDO::PARAM_INT);
        $stmt->bindValue(':routing_id', $committeeRoutingId, PDO::PARAM_INT);
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
        unset($doc);

        return $documents;
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
