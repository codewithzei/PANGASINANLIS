<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/routing_option.php';
require_once __DIR__ . '/../../classes/audit_logger.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['routing_option_status'] = 'error';
    $_SESSION['routing_option_message'] = 'Unauthorized action. Please login first.';
    header('Location: /PangasinanLIS/pages/admin/routing_options');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $routingOptionId = intval($_POST['routing_option_id'] ?? 0);
    $currentUserId   = $_SESSION['user_id'];

    if (empty($routingOptionId)) {
        $_SESSION['routing_option_status'] = 'error';
        $_SESSION['routing_option_message'] = 'Invalid routing option ID.';
        header('Location: /PangasinanLIS/pages/admin/routing_options');
        exit;
    }

    // Fetch name from DB for accurate audit label
    $optionName = "Routing Option #$routingOptionId";
    try {
        $stmt = $pdo->prepare("SELECT routing_option_name FROM routing_options WHERE routing_option_id = :id AND is_deleted = FALSE");
        $stmt->execute([':id' => $routingOptionId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $optionName = $row['routing_option_name']; }
    } catch (PDOException $e) {}

    $routingOptionObj = new RoutingOption($pdo);
    $result = $routingOptionObj->deleteRoutingOption($routingOptionId, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROUTING_OPTION_MGMT',
            'DELETE',
            $routingOptionId,
            "Deleted routing option: $optionName"
        );
    }

    $_SESSION['routing_option_status']  = $result['status'];
    $_SESSION['routing_option_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/routing_options');
    exit;
}
?>