<?php
session_start();
include '../../includes/db.php';
include '../../includes/badges.php';
include '../../includes/fetch_committee_hearings_data.php';
include '../../includes/fetch_for_opinion_data.php';
include '../../components/header.php';

$userId = $_SESSION['user_id'] ?? null;
$documentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

if (!$documentId) {
    header('Location: committee_hearings');
    exit;
}

$doc = getHearingDocumentById($documentId, $userId);

if (!$doc) {
    header('Location: committee_hearings');
    exit;
}

if (($doc['status_name'] ?? '') !== 'On Going') {
    header('Location: view_hearing_document?id=' . (int) $documentId);
    exit;
}

$hearingOutcomeStatuses = [];
try {
    $stStmt = $pdo->query('SELECT document_status_id, document_status_name FROM document_statuses WHERE COALESCE(is_deleted, 0) = 0');
    $allStatuses = $stStmt ? $stStmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $wantedOrder = ['Approved', 'Deferred', 'Remanded', 'Withdrawn'];
    foreach ($wantedOrder as $wantName) {
        foreach ($allStatuses as $s) {
            if (($s['document_status_name'] ?? '') === $wantName) {
                $hearingOutcomeStatuses[] = $s;
                break;
            }
        }
    }
} catch (PDOException $e) {
    $hearingOutcomeStatuses = [];
}

$opinionDoc = getForOpinionDocumentById($documentId, $userId);
$doc['endorsements'] = ($opinionDoc && isset($opinionDoc['endorsements']) && is_array($opinionDoc['endorsements']))
    ? $opinionDoc['endorsements']
    : [];

$trackingHistory = [];
$historyError = null;
try {
    $historyStmt = $pdo->prepare("
        SELECT 
            dh.action AS action_taken, 
            dh.remarks, 
            dh.created_at, 
            CONCAT(ui.first_name, ' ', ui.last_name) AS processed_by_name,
            ur.user_role_name AS role_name
        FROM document_history dh
        LEFT JOIN user_info ui ON dh.user_id = ui.user_account_id
        LEFT JOIN user_accounts ua ON dh.user_id = ua.user_account_id
        LEFT JOIN user_roles ur ON ua.user_role_id = ur.user_role_id
        WHERE dh.document_id = :document_id 
        ORDER BY dh.created_at DESC
    ");
    $historyStmt->execute([':document_id' => $documentId]);
    $trackingHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $historyError = $e->getMessage();
}

$mainSource = !empty($doc['source_name']) ? $doc['source_name'] : ($doc['source_type_name'] ?? 'N/A');
$location = '';
if (!empty($doc['muni_city_name'])) {
    $location = ' (' . $doc['muni_city_name'] . ')';
} elseif (!empty($doc['hospital_name'])) {
    $location = ' - ' . $doc['hospital_name'];
} elseif (!empty($doc['external_office_name'])) {
    if (empty($doc['source_name'])) {
        $mainSource = $doc['external_office_name'];
    } else {
        $location = ' - ' . $doc['external_office_name'];
    }
}
$formattedSource = $mainSource . $location;

$agendaDateDisplay = !empty($doc['agenda_date']) ? date('F d, Y', strtotime($doc['agenda_date'])) : 'N/A';
$agendaTimeRaw = $doc['agenda_time'] ?? '';
$agendaTimeDisplay = 'N/A';
if ($agendaTimeRaw !== '' && $agendaTimeRaw !== null) {
    $ts = strtotime($agendaTimeRaw);
    $agendaTimeDisplay = $ts ? date('g:i A', $ts) : htmlspecialchars((string) $agendaTimeRaw);
}

$opinionBadgeMap = [
    1 => 'bg-blue-100 text-blue-800',
    2 => 'bg-green-100 text-green-800',
    3 => 'bg-amber-100 text-amber-700',
    4 => 'bg-cyan-100 text-cyan-800',
];
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">
        <?php
        $pageTitle = 'Process hearing: ' . htmlspecialchars($doc['tracking_number']);
        include '../../components/topbar.php';
        ?>

        <main class="p-4 sm:p-6 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">

                    

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0 bg-slate-50">
                            <div class="flex items-center gap-3">
                                <a href="committee_hearings" class="p-1.5 text-slate-400 hover:text-[#0033A1] hover:bg-blue-50 rounded-lg transition-all" title="Back to Committee Hearings">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                                <h2 class="text-xl font-semibold text-gray-800">Document Information</h2>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="col-span-1 sm:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100 flex items-center justify-between gap-4">
                                    <span class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Tracking Number</span>
                                    <span class="text-2xl font-black text-[#0033A1] tracking-wide"><?php echo htmlspecialchars($doc['tracking_number']); ?></span>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Current Status</label>
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border <?php echo getStatusBadgeClass($doc['status_name'] ?? ''); ?> uppercase">
                                        <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Current Division</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['current_division'] ?? 'N/A'); ?></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Legislative Cycle</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars((string) ($doc['cycle'] ?? '1')); ?></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date Created</label>
                                    <p class="text-slate-800 font-medium"><?php echo isset($doc['created_at']) ? date('F d, Y - h:i A', strtotime($doc['created_at'])) : 'N/A'; ?></p>
                                </div>

                                <?php if (!empty($doc['communication_category_name'])): ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Communication Category</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['communication_category_name']); ?></p>
                                </div>
                                <?php endif; ?>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Document Type</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Source / Origin</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($formattedSource); ?></p>
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Subject Matter</label>
                                    <div class="p-4 bg-slate-50 rounded border border-slate-200 text-slate-800 font-medium whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($doc['subject_matter']); ?></div>
                                </div>

                                <div class="col-span-1 sm:col-span-2 pt-4 border-t border-slate-100">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Main Attachments</label>
                                    <?php if (!empty($doc['attachments'])): ?>
                                        <ul class="flex flex-col gap-2">
                                            <?php foreach ($doc['attachments'] as $file):
                                                $filePath = '../../' . htmlspecialchars($file['file_path']);
                                                $fileName = htmlspecialchars($file['file_name']);
                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                if ($ext === 'pdf') { $ic = 'text-red-500'; }
                                                elseif (in_array($ext, ['doc', 'docx'])) { $ic = 'text-indigo-500'; }
                                                elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $ic = 'text-green-600'; }
                                                else { $ic = 'text-blue-500'; }
                                            ?>
                                                <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm transition-colors hover:bg-blue-50 hover:border-blue-200 cursor-pointer"
                                                    onclick="window.open('<?php echo $filePath; ?>', '_blank')">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 <?php echo $ic; ?> shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                        </svg>
                                                        <div class="min-w-0">
                                                            <p class="font-medium text-slate-700 truncate"><?php echo $fileName; ?></p>
                                                            <p class="text-xs text-blue-500 mt-0.5">Click to view document</p>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo $filePath; ?>" download
                                                        class="shrink-0 text-slate-400 hover:text-[#0033A1] transition-colors p-2"
                                                        title="Download file"
                                                        onclick="event.stopPropagation();">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 text-sm text-slate-500 italic flex items-center justify-center gap-2">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No main file attached to this document.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-slate-50">
                            <h2 class="text-lg font-semibold text-gray-800">Opinion Workflow</h2>
                            <?php
                            $endorsements = $doc['endorsements'] ?? [];

                            // 1. I-group natin by office at kunin ang LATEST round per office
                            $latestPerOffice = [];

                            foreach ($endorsements as $e) {
                                // Siguraduhing tama itong key base sa database mo
                                $officeId = $e['opinion_office_id']; 
                                $round = (int) ($e['endorsement_round'] ?? 1);

                                // Kung wala pa sa listahan, O KUNG mas mataas ang round nitong kasalukuyang loop, i-update natin
                                if (!isset($latestPerOffice[$officeId]) || $round > $latestPerOffice[$officeId]['round']) {
                                    $latestPerOffice[$officeId] = [
                                        'status' => (int) ($e['opinion_status_id'] ?? 0),
                                        'round' => $round
                                    ];
                                }
                            }

                            // 2. Ngayon natin bilangin yung mga favorable (status == 2) mula doon sa unique offices
                            $favorable = 0;
                            foreach ($latestPerOffice as $office) {
                                if ($office['status'] == 2) {
                                    $favorable++;
                                }
                            }

                            // 3. Ang total natin ngayon ay yung bilang ng unique offices na lang
                            $total = count($latestPerOffice);
                            $pct = $total > 0 ? round(($favorable / $total) * 100) : 0;
                            ?>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-500"><?php echo (int) $favorable; ?>/<?php echo (int) $total; ?> Favorable</span>
                                <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width: <?php echo (int) $pct; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm text-gray-700">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Office</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Latest Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    <?php if (empty($endorsements)): ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-6 text-center text-sm text-slate-400 italic">No endorsements found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($endorsements as $office):
                                            $round = $office['endorsement_round'] ?? 1;
                                            $statusId = (int) ($office['opinion_status_id'] ?? 1);
                                            $badgeClass = $opinionBadgeMap[$statusId] ?? 'bg-gray-100 text-gray-700';
                                            $hasAttachments = !empty($office['opinion_attachment']) || !empty($office['compliance_attachment']);
                                            $rowBorderClass = $hasAttachments ? 'border-b-0' : 'border-b border-slate-100';
                                            if ($statusId === 1) {
                                                $latestAction = 'Awaiting initial opinion';
                                            } elseif ($statusId === 2) {
                                                $latestAction = !empty($office['opinion_remarks']) ? 'Favorable: ' . $office['opinion_remarks'] : 'Opinion submitted (Favorable)';
                                            } elseif ($statusId === 3) {
                                                $latestAction = !empty($office['opinion_remarks']) ? 'Unfavorable: ' . $office['opinion_remarks'] : 'Opinion submitted (Unfavorable)';
                                            } elseif ($statusId === 4) {
                                                $latestAction = !empty($office['compliance_description']) ? 'Compliance submitted: ' . $office['compliance_description'] : 'Compliance submitted. Awaiting 2nd opinion.';
                                            } else {
                                                $latestAction = '—';
                                            }
                                        ?>
                                            <tr class="hover:bg-slate-50 transition-colors duration-150 <?php echo $rowBorderClass; ?>">
                                                <td class="px-6 py-4 text-gray-800 break-words min-w-48 font-medium">
                                                    <?php echo htmlspecialchars($office['opinion_office_name'] ?? '—'); ?>
                                                    <?php if ((int) $round > 1): ?>
                                                        <span class="block text-[10px] text-gray-400 font-normal tracking-wide uppercase mt-0.5">Round <?php echo (int) $round; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full <?php echo $badgeClass; ?>">
                                                        <?php echo strtoupper(htmlspecialchars($office['opinion_status_name'] ?? '—')); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 break-words min-w-48">
                                                    <?php echo htmlspecialchars($latestAction); ?>
                                                </td>
                                            </tr>
                                            <?php if ($hasAttachments): ?>
                                            <tr class="border-b border-slate-200 bg-white">
                                                <td colspan="3" class="px-6 pb-5 pt-1">
                                                    <div class="border-slate-200 ml-2">
                                                        <ul class="flex flex-col gap-2">
                                                            <?php if (!empty($office['opinion_attachment'])):
                                                                $opPath = '../../assets/uploads/opinions/' . htmlspecialchars($office['opinion_attachment']);
                                                                $opName = htmlspecialchars($office['opinion_attachment']);
                                                                $opExt = strtolower(pathinfo($opName, PATHINFO_EXTENSION));
                                                                if ($opExt === 'pdf') { $opIc = 'text-red-500'; }
                                                                elseif (in_array($opExt, ['doc', 'docx'])) { $opIc = 'text-indigo-500'; }
                                                                elseif (in_array($opExt, ['xls', 'xlsx', 'csv'])) { $opIc = 'text-green-600'; }
                                                                else { $opIc = 'text-blue-500'; }
                                                            ?>
                                                                <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm transition-colors hover:bg-blue-50 hover:border-blue-200 cursor-pointer" onclick="window.open('<?php echo $opPath; ?>', '_blank')">
                                                                    <div class="flex items-center gap-3 min-w-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 <?php echo $opIc; ?> shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                                        </svg>
                                                                        <div class="min-w-0">
                                                                            <p class="font-medium text-slate-700 truncate"><?php echo $opName; ?></p>
                                                                            <p class="text-xs text-blue-500 mt-0.5">Opinion File</p>
                                                                        </div>
                                                                    </div>
                                                                    <a href="<?php echo $opPath; ?>" download class="shrink-0 text-slate-400 hover:text-[#0033A1] p-2 transition-colors" onclick="event.stopPropagation();">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if (!empty($office['compliance_attachment'])):
                                                                $compPath = '../../assets/uploads/compliances/' . htmlspecialchars($office['compliance_attachment']);
                                                                $compName = htmlspecialchars($office['compliance_attachment']);
                                                                $compExt = strtolower(pathinfo($compName, PATHINFO_EXTENSION));
                                                                if ($compExt === 'pdf') { $compIc = 'text-red-500'; }
                                                                elseif (in_array($compExt, ['doc', 'docx'])) { $compIc = 'text-indigo-500'; }
                                                                elseif (in_array($compExt, ['xls', 'xlsx', 'csv'])) { $compIc = 'text-green-600'; }
                                                                else { $compIc = 'text-blue-500'; }
                                                            ?>
                                                                <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm transition-colors hover:bg-amber-50 hover:border-amber-200 cursor-pointer" onclick="window.open('<?php echo $compPath; ?>', '_blank')">
                                                                    <div class="flex items-center gap-3 min-w-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 <?php echo $compIc; ?> shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                                        </svg>
                                                                        <div class="min-w-0">
                                                                            <p class="font-medium text-slate-700 truncate"><?php echo $compName; ?></p>
                                                                            <p class="text-xs text-amber-600 mt-0.5">Compliance File</p>
                                                                        </div>
                                                                    </div>
                                                                    <a href="<?php echo $compPath; ?>" download class="shrink-0 text-slate-400 hover:text-amber-700 p-2 transition-colors" onclick="event.stopPropagation();">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-slate-50">
                            <h2 class="text-lg font-semibold text-gray-800">Scheduled Hering (Agenda)</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Committee</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($doc['committee_name'] ?? 'N/A'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agenda number</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Agenda type</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($doc['agenda_type'] ?? 'N/A'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Chairperson</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($agendaDateDisplay); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Time</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($agendaTimeDisplay); ?></dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Venue</dt>
                                    <dd class="mt-1 text-slate-800 font-medium"><?php echo htmlspecialchars($doc['venue'] ?? 'N/A'); ?></dd>
                                </div>
                                <?php if (!empty($doc['agenda_notes'])): ?>
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Notes</dt>
                                    <dd class="text-slate-800 whitespace-pre-wrap p-3 bg-slate-50 rounded border border-slate-100"><?php echo htmlspecialchars($doc['agenda_notes']); ?></dd>
                                </div>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Hearing Outcome</h2>
                        </div>
                        <div class="p-6">
                            <?php if (empty($hearingOutcomeStatuses)): ?>
                                <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-4">No hearing outcome statuses are configured. Add Approved, Deferred, Remanded, and Withdrawn in document statuses.</p>
                            <?php else: ?>
                                <form id="hearingOutcomeForm" class="space-y-4">
                                    <input type="hidden" name="document_id" value="<?php echo (int) $documentId; ?>">
                                    <div>
                                        <label for="document_status_id" class="block text-xs font-semibold text-slate-500 tracking-wider mb-2">Select Hearing Result</label>
                                        <select id="document_status_id" name="document_status_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#0033A1] focus:border-transparent outline-none">
                                            <option value="">Select outcome…</option>
                                            <?php foreach ($hearingOutcomeStatuses as $st): ?>
                                                <option value="<?php echo (int) $st['document_status_id']; ?>"><?php echo htmlspecialchars($st['document_status_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mt-6">
                                        <!-- Binago ang classes dito: idinagdag ang w-full, flex, at justify-center -->
                                        <button type="submit" id="hearingOutcomeSubmit" class="w-full flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-[#0033A1] hover:bg-blue-900 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            Confirm Result
                                        </button>
                                    </div>
                                    <p id="hearingOutcomeMsg" class="text-sm hidden" role="alert"></p>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-slate-50 rounded-t-xl shrink-0">
                            <h2 class="text-lg font-semibold text-gray-800">Tracking History</h2>
                        </div>
                        <div class="p-6">
                            <?php if ($historyError): ?>
                                <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                                    <strong>Error:</strong> <?php echo htmlspecialchars($historyError); ?>
                                </div>
                            <?php endif; ?>

                            <ol class="relative border-l-2 border-blue-200 ml-2.5">
                                <?php if (empty($trackingHistory) && !$historyError): ?>
                                    <li class="mb-6 ml-6">
                                        <p class="text-sm font-normal text-slate-500 italic">No tracking history available yet.</p>
                                    </li>
                                <?php else: ?>
                                    <?php foreach ($trackingHistory as $index => $history):
                                        $isLatest = ($index === 0);
                                        $dotColor = $isLatest ? 'bg-[#0033A1]' : 'bg-slate-400';
                                        $ringColor = $isLatest ? 'bg-blue-100' : 'bg-slate-100';
                                        $actionName = htmlspecialchars($history['action_taken'] ?? 'System Update');
                                        $remarksH = htmlspecialchars($history['remarks'] ?? '');
                                        $processedBy = htmlspecialchars($history['processed_by_name'] ?? '');
                                        $roleName = htmlspecialchars($history['role_name'] ?? 'System');
                                        $dateFmt = isset($history['created_at']) ? date('M d, Y - h:i A', strtotime($history['created_at'])) : 'Unknown Date';
                                    ?>
                                        <li class="mb-6 ml-6">
                                            <span class="absolute flex items-center justify-center w-5 h-5 <?php echo $ringColor; ?> rounded-full -left-[11px] ring-4 ring-white">
                                                <div class="w-2.5 h-2.5 <?php echo $dotColor; ?> rounded-full"></div>
                                            </span>
                                            <h3 class="flex items-center mb-1 text-sm font-semibold text-gray-900">
                                                <?php echo $actionName; ?>
                                                <?php if ($isLatest): ?>
                                                    <span class="bg-blue-100 text-[#0033A1] text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded ml-3">Latest</span>
                                                <?php endif; ?>
                                            </h3>
                                            <time class="block mb-2 text-xs font-normal leading-none text-slate-400">
                                                <?php echo htmlspecialchars($dateFmt); ?>
                                                <?php if (!empty($processedBy)): ?>
                                                    <span class="mx-1">•</span> By: <span class="font-medium text-slate-700"><?php echo $processedBy; ?></span> <span class="italic">(<?php echo $roleName; ?>)</span>
                                                <?php endif; ?>
                                            </time>
                                            <?php if (!empty($remarksH)): ?>
                                                <p class="text-sm font-normal text-slate-600 bg-slate-50 p-2 rounded border border-slate-100 mt-2">"<?php echo $remarksH; ?>"</p>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ol>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script src="/PangasinanLIS/src/js/global.js"></script>
<script src="/PangasinanLIS/src/js/page_transition.js"></script>
<script>
(function () {
    var form = document.getElementById('hearingOutcomeForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = document.getElementById('hearingOutcomeSubmit');
        var msg = document.getElementById('hearingOutcomeMsg');
        if (btn) btn.disabled = true;
        if (msg) {
            msg.classList.add('hidden');
            msg.textContent = '';
        }
        var fd = new FormData(form);
        fetch('../../includes/actions_committee/process_hearing_document.php', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    window.location.href = 'view_hearing_document?id=<?php echo (int) $documentId; ?>';
                    return;
                }
                if (msg) {
                    msg.textContent = data.message || 'Could not save.';
                    msg.className = 'text-sm text-red-600';
                    msg.classList.remove('hidden');
                }
                if (btn) btn.disabled = false;
            })
            .catch(function () {
                if (msg) {
                    msg.textContent = 'Network error. Please try again.';
                    msg.className = 'text-sm text-red-600';
                    msg.classList.remove('hidden');
                }
                if (btn) btn.disabled = false;
            });
    });
})();
</script>
</body>
</html>
