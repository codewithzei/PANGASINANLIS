<?php
class AuditLogger {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function log($userId, $type, $module, $action, $recordId = null, $details = '') {
        $sql = "INSERT INTO audit_logs (user_id, log_type, module, action, record_id, details, ip_address) 
                VALUES (:user_id, :type, :module, :action, :record_id, :details, :ip)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'   => $userId,
            ':type'      => $type,
            ':module'    => $module,
            ':action'    => $action,
            ':record_id' => $recordId,
            ':details'   => $details,
            ':ip'        => $_SERVER['REMOTE_ADDR']
        ]);
    }
}