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
    $routingOptionName = trim($_POST['routing_option_name'] ?? '');
    $currentUserId     = $_SESSION['user_id'];

    if (empty($routingOptionName)) {
        $_SESSION['routing_option_status'] = 'error';
        $_SESSION['routing_option_message'] = 'Please enter a routing option name.';
        header('Location: /PangasinanLIS/pages/admin/routing_options');
        exit;
    }

    $routingOptionObj = new RoutingOption($pdo);
    $result = $routingOptionObj->createRoutingOption($routingOptionName, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROUTING_OPTION_MGMT',
            'CREATE',
            null,
            "Created routing option: $routingOptionName"
        );
    }

    $_SESSION['routing_option_status']  = $result['status'];
    $_SESSION['routing_option_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/routing_options');
    exit;
}
?>