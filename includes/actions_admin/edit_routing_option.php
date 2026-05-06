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
    $routingOptionId   = intval($_POST['routing_option_id'] ?? 0);
    $routingOptionName = trim($_POST['routing_option_name'] ?? '');
    $status            = intval($_POST['data_status_id'] ?? 1);
    $currentUserId     = $_SESSION['user_id'];

    if (empty($routingOptionId)) {
        $_SESSION['routing_option_status'] = 'error';
        $_SESSION['routing_option_message'] = 'Invalid routing option ID.';
        header('Location: /PangasinanLIS/pages/admin/routing_options');
        exit;
    }

    if (empty($routingOptionName)) {
        $_SESSION['routing_option_status'] = 'error';
        $_SESSION['routing_option_message'] = 'Please enter a routing option name.';
        header('Location: /PangasinanLIS/pages/admin/routing_options');
        exit;
    }

    if ($status <= 0) {
        $_SESSION['routing_option_status'] = 'error';
        $_SESSION['routing_option_message'] = 'Please select a valid status.';
        header('Location: /PangasinanLIS/pages/admin/routing_options');
        exit;
    }

    $routingOptionObj = new RoutingOption($pdo);
    $result = $routingOptionObj->updateRoutingOption($routingOptionId, $routingOptionName, $status, $currentUserId);

    if ($result['status'] === 'success') {
        $audit = new AuditLogger($pdo);
        $audit->log(
            $currentUserId,
            'DATA_CHANGE',
            'ROUTING_OPTION_MGMT',
            'UPDATE',
            $routingOptionId,
            "Updated routing option: $routingOptionName"
        );
    }

    $_SESSION['routing_option_status']  = $result['status'];
    $_SESSION['routing_option_message'] = $result['message'];

    header('Location: /PangasinanLIS/pages/admin/routing_options');
    exit;
}
?>