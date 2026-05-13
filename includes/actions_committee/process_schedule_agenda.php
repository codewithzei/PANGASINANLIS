<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../../classes/Document.php'; // Siguraduhing tama ang capitalization ng file mo

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT);

if (!$userId || !$documentId) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized action or missing document.']);
    exit;
}

// BAGO: Idinagdag ang 'venue' sa data payload
$data = [
    'committee_id' => filter_input(INPUT_POST, 'committee_id', FILTER_VALIDATE_INT),
    'agenda_number' => isset($_POST['agenda_number']) ? trim((string) $_POST['agenda_number']) : '',
    'agenda_type' => isset($_POST['agenda_type']) ? trim((string) $_POST['agenda_type']) : '',
    'chairperson' => isset($_POST['chairperson']) ? trim((string) $_POST['chairperson']) : '',
    'agenda_date' => isset($_POST['agenda_date']) ? trim((string) $_POST['agenda_date']) : '',
    'agenda_time' => isset($_POST['agenda_time']) ? trim((string) $_POST['agenda_time']) : '',
    'venue' => isset($_POST['venue']) ? trim((string) $_POST['venue']) : '', 
    'notes' => isset($_POST['notes']) ? trim((string) $_POST['notes']) : '',
];

$docObj = new Document($pdo);
$result = $docObj->scheduleAgenda($documentId, (int) $userId, $data);

echo json_encode($result);
exit;