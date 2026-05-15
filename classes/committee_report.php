<?php

class CommitteeReport
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a committee report from HTTP POST-shaped input.
     *
     * @param array $post Typically $_POST
     * @param int   $userId Session user id (created_by)
     * @return array{status: string, message: string, committee_report_id?: int}
     */
    public function createFromPost(array $post, int $userId): array
    {
        $reportTitle = isset($post['report_title']) ? trim((string) $post['report_title']) : '';
        $reportTypeId = isset($post['report_type_id']) ? filter_var($post['report_type_id'], FILTER_VALIDATE_INT) : false;
        $committeeReportNumber = isset($post['report_number']) ? trim((string) $post['report_number']) : '';
        $jointReportNumber = isset($post['joint_report_number']) ? trim((string) $post['joint_report_number']) : '';
        $dateOfApproval = isset($post['date_of_approval']) ? trim((string) $post['date_of_approval']) : '';
        $summary = isset($post['summary_of_findings']) ? trim((string) $post['summary_of_findings']) : '';

        $docIds = [];
        if (!empty($post['selected_docs']) && is_array($post['selected_docs'])) {
            foreach ($post['selected_docs'] as $raw) {
                $id = filter_var($raw, FILTER_VALIDATE_INT);
                if ($id) {
                    $docIds[$id] = true;
                }
            }
        }
        $docIds = array_keys($docIds);

        $committeeIds = [];
        if (!empty($post['committee_id']) && is_array($post['committee_id'])) {
            foreach ($post['committee_id'] as $raw) {
                $id = filter_var($raw, FILTER_VALIDATE_INT);
                if ($id) {
                    $committeeIds[$id] = true;
                }
            }
        }
        $committeeIds = array_keys($committeeIds);

        if ($reportTitle === '' || !$reportTypeId || $committeeReportNumber === '' || $dateOfApproval === '' || $summary === '') {
            return ['status' => 'error', 'message' => 'Please fill in all required report fields.'];
        }

        if (empty($docIds)) {
            return ['status' => 'error', 'message' => 'Select at least one approved document.'];
        }

        if (empty($committeeIds)) {
            return ['status' => 'error', 'message' => 'Select at least one committee in charge.'];
        }

        $dApproval = DateTime::createFromFormat('Y-m-d', $dateOfApproval);
        if (!$dApproval || $dApproval->format('Y-m-d') !== $dateOfApproval) {
            return ['status' => 'error', 'message' => 'Invalid date of approval.'];
        }

        try {
            $rtStmt = $this->pdo->prepare('SELECT report_type_id, report_type_name FROM report_types WHERE report_type_id = :id AND COALESCE(is_deleted, 0) = 0 AND status = :active LIMIT 1');
            $rtStmt->execute([':id' => $reportTypeId, ':active' => 'active']);
            $reportTypeRow = $rtStmt->fetch(PDO::FETCH_ASSOC);
            if (!$reportTypeRow) {
                return ['status' => 'error', 'message' => 'Invalid or inactive report type.'];
            }

            $typeName = strtolower((string) ($reportTypeRow['report_type_name'] ?? ''));
            $isJointType = (stripos($typeName, 'joint') !== false);

            if ($isJointType && $jointReportNumber === '') {
                return ['status' => 'error', 'message' => 'Joint committee report number is required for this report type.'];
            }

            $jointDb = ($isJointType && $jointReportNumber !== '') ? $jointReportNumber : null;

            $placeholders = implode(',', array_fill(0, count($docIds), '?'));
            $verifySql = "
                SELECT d.document_id
                FROM documents d
                INNER JOIN agendas a ON d.document_id = a.document_id
                LEFT JOIN document_statuses ds ON d.status = ds.document_status_id
                WHERE d.current_owner_user_id = ?
                  AND ds.document_status_name = 'Approved'
                  AND d.document_id IN ($placeholders)
            ";
            $verifyStmt = $this->pdo->prepare($verifySql);
            $verifyStmt->execute(array_merge([$userId], $docIds));
            $allowed = array_map('intval', $verifyStmt->fetchAll(PDO::FETCH_COLUMN));

            if (count($allowed) !== count($docIds)) {
                return ['status' => 'error', 'message' => 'One or more documents are not available for reporting (must be approved and assigned to you).'];
            }

            $cPlaceholders = implode(',', array_fill(0, count($committeeIds), '?'));
            $cVerify = $this->pdo->prepare("SELECT COUNT(*) FROM committees WHERE committee_id IN ($cPlaceholders) AND COALESCE(is_deleted, 0) = 0 AND status = 'active'");
            $cVerify->execute($committeeIds);
            if ((int) $cVerify->fetchColumn() !== count($committeeIds)) {
                return ['status' => 'error', 'message' => 'One or more selected committees are invalid or inactive.'];
            }

            $this->pdo->beginTransaction();

            $ins = $this->pdo->prepare('
                INSERT INTO committee_reports (
                    report_title,
                    report_type_id,
                    committee_report_number,
                    joint_committee_report_number,
                    date_of_approval,
                    summary_of_findings,
                    created_by
                ) VALUES (
                    :report_title,
                    :report_type_id,
                    :committee_report_number,
                    :joint_committee_report_number,
                    :date_of_approval,
                    :summary_of_findings,
                    :created_by
                )
            ');
            $ins->execute([
                ':report_title' => $reportTitle,
                ':report_type_id' => $reportTypeId,
                ':committee_report_number' => $committeeReportNumber,
                ':joint_committee_report_number' => $jointDb,
                ':date_of_approval' => $dateOfApproval,
                ':summary_of_findings' => $summary,
                ':created_by' => $userId,
            ]);

            $newReportId = (int) $this->pdo->lastInsertId();

            $insDoc = $this->pdo->prepare('INSERT INTO committee_report_documents (committee_report_id, document_id) VALUES (:rid, :did)');
            foreach ($docIds as $did) {
                $insDoc->execute([':rid' => $newReportId, ':did' => (int) $did]);
            }

            $insCom = $this->pdo->prepare('INSERT INTO committee_report_committees (committee_report_id, committee_id) VALUES (:rid, :cid)');
            foreach ($committeeIds as $cid) {
                $insCom->execute([':rid' => $newReportId, ':cid' => (int) $cid]);
            }

            $this->pdo->commit();

            return [
                'status' => 'success',
                'message' => 'Committee report created successfully.',
                'committee_report_id' => $newReportId,
            ];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['status' => 'error', 'message' => 'Could not save the report. Please try again.'];
        }
    }
}
