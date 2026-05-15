<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

/**
 * One row per document linked to a committee report (bridge `committee_report_documents`),
 * for reports created by the given user (`created_by`).
 *
 * @return array<int, array<string, mixed>>|array{error: string}
 */
function getCommitteeReportsDataForUser(int $userId)
{
    global $pdo;

    try {
        $sql = "
            SELECT
                cr.committee_report_id,
                cr.report_title,
                cr.committee_report_number,
                cr.joint_committee_report_number,
                cr.date_of_approval,
                cr.created_at,
                rt.report_type_name,
                d.document_id,
                d.tracking_number,
                d.subject_matter,
                d.version AS cycle,
                ds.document_status_name AS status_name,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.committee_name ORDER BY c.committee_name SEPARATOR ', ')
                    FROM committee_report_committees crc
                    INNER JOIN committees c ON c.committee_id = crc.committee_id AND COALESCE(c.is_deleted, 0) = 0
                    WHERE crc.committee_report_id = cr.committee_report_id
                ) AS committees_display
            FROM committee_report_documents crd
            INNER JOIN committee_reports cr ON cr.committee_report_id = crd.committee_report_id
            INNER JOIN report_types rt ON cr.report_type_id = rt.report_type_id
            INNER JOIN documents d ON d.document_id = crd.document_id
            LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
            WHERE cr.created_by = :uid
            ORDER BY cr.created_at DESC, d.document_id ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            if (!empty($r['date_of_approval'])) {
                $r['date_display'] = date('M j, Y', strtotime($r['date_of_approval']));
            } else {
                $r['date_display'] = 'N/A';
            }
        }
        unset($r);

        return $rows;
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
