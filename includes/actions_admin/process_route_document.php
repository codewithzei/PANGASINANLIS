<?php
session_start();
require_once __DIR__ . '/../db.php'; // Siguraduhin na tama ang path ng db.php mo
require_once __DIR__ . '/../../classes/document.php'; // Siguraduhin na tama ang path papunta sa class

// Security check
if (!isset($_SESSION['user_id'])) {
    $_SESSION['route_status'] = 'error';
    $_SESSION['route_message'] = 'Unauthorized access. Please login.';
    header('Location: ../../../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'];
    $docObj = new Document($pdo);

    // 1. CONSOLIDATE DYNAMIC SOURCE NAME
    // Titingnan natin alin sa tatlong text fields ang may laman
    $sourceName = '';
    if (!empty($_POST['clientName'])) {
        $sourceName = trim($_POST['clientName']);
    } elseif (!empty($_POST['spMember'])) {
        $sourceName = trim($_POST['spMember']);
    } elseif (!empty($_POST['agency'])) {
        $sourceName = trim($_POST['agency']);
    }

    // 2. KUNIN ANG MAIN DATA (Gamit ang exact names mula sa HTML mo)
    $documentData = [
        'date_received' => $_POST['dateReceived'] ?? date('Y-m-d'),
        'subject_matter' => trim($_POST['subjectMatter'] ?? ''),
        'document_type_id' => intval($_POST['documentType'] ?? 0),
        'source_type_id' => intval($_POST['sourceType'] ?? 0),
        'source_name' => $sourceName, // Yung pinagsama natin sa taas
        'source_external_office_id' => !empty($_POST['externalOffice']) ? intval($_POST['externalOffice']) : null,
        'source_hospital_id' => !empty($_POST['hospital']) ? intval($_POST['hospital']) : null,
        'muni_city_id' => !empty($_POST['muniCity']) ? intval($_POST['muniCity']) : null,
        'routing_option_id' => intval($_POST['routingOption'] ?? 0),
        'communicationCategory' => !empty($_POST['communicationCategory']) ? intval($_POST['communicationCategory']) : null,
        'remarks' => trim($_POST['remarks'] ?? '')
    ];

    // 3. KUNIN ANG CHECKBOXES (requirements[])
    $requirements = $_POST['requirements'] ?? [];

    // 4. I-HANDLE ANG MULTIPLE FILE UPLOADS
    $uploadedFiles = [];
    $uploadDirectory = __DIR__ . '/../../assets/documents/';

    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $fileCount = count($_FILES['attachments']['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            $originalName = $_FILES['attachments']['name'][$i];
            $tmpName = $_FILES['attachments']['tmp_name'][$i];

            $newFileName = time() . '_' . basename($originalName);
            $destination = $uploadDirectory . $newFileName;

            $dbFilePath = 'assets/documents/' . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFiles[] = [
                    'name' => $originalName,
                    'path' => $dbFilePath
                ];
            }
        }
    }

    // 5. IPASA LAHAT SA DOCUMENT CLASS
    // Gagamitin natin yung createDocument function kasi "New Document" ang form mo
    $result = $docObj->createDocument($documentData, $requirements, $uploadedFiles, $currentUserId);

    // 6. I-SET ANG SESSION MESSAGE AT I-REDIRECT
    $_SESSION['route_status'] = $result['status'];
    $_SESSION['route_message'] = $result['message'];

    // I-redirect pabalik sa page mo (i-adjust mo if kailangan)
    header('Location: /PangasinanLIS/pages/admin/routed_documents');
    exit;
}
?>