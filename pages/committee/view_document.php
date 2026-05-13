<?php
session_start();
include '../../includes/db.php';
include '../../includes/badges.php';
include '../../includes/fetch_referred_documents_data.php';

include '../../components/header.php';

$userId = $_SESSION['user_id'] ?? null;
$documentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

if (!$documentId) {
    header('Location: referred_documents');
    exit;
}

$doc = getReferredDocumentById($documentId, $userId);

if (!$doc) {
    header('Location: referred_documents');
    exit;
}

// Tracking History
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

// Source formatting
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
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">
        <?php
        $pageTitle = 'View Document: ' . htmlspecialchars($doc['tracking_number']);
        include '../../components/topbar.php';
        ?>

        <main class="p-4 sm:p-6 w-full">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT COLUMN: Document Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0 bg-slate-50">
                            <div class="flex items-center gap-3">
                                <a href="referred_documents" class="p-1.5 text-slate-400 hover:text-[#0033A1] hover:bg-blue-50 rounded-lg transition-all" title="Back to Referred Documents">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                                <h2 class="text-xl font-semibold text-gray-800">Document Information</h2>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                                <!-- Tracking No Banner -->
                                <div class="col-span-1 sm:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100 flex items-center justify-between gap-4">
                                    <span class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Tracking Number</span>
                                    <span class="text-2xl font-black text-[#0033A1] tracking-wide"><?php echo htmlspecialchars($doc['tracking_number']); ?></span>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Current Status</label>
                                    <?php $bgClass = getStatusBadgeClass($doc['status_name']); ?>
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border <?php echo $bgClass; ?> uppercase">
                                        <?php echo htmlspecialchars($doc['status_name']); ?>
                                    </span>
                                </div>

                                <!-- Cycle -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Cycle / Version</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['version'] ?? '1'); ?></p>
                                </div>

                                <!-- Current Division -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Current Division</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['current_division'] ?? 'N/A'); ?></p>
                                </div>

                                <!-- Date Created -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date Created in System</label>
                                    <p class="text-slate-800 font-medium"><?php echo isset($doc['created_at']) ? date('F d, Y - h:i A', strtotime($doc['created_at'])) : 'N/A'; ?></p>
                                </div>

                                <!-- Subject Matter -->
                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Subject Matter</label>
                                    <div class="p-4 bg-slate-50 rounded border border-slate-200 text-slate-800 font-medium whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($doc['subject_matter']); ?></div>
                                </div>

                                <!-- Document Type -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Document Type</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?></p>
                                </div>

                                <!-- Source -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Source / Origin</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($formattedSource); ?></p>
                                </div>

                                <!-- Attachments -->
                                <div class="col-span-1 sm:col-span-2 pt-4 border-t border-slate-100">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Attachments</label>
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
                                                <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm transition-colors hover:bg-blue-50 hover:border-blue-200 cursor-pointer" onclick="window.open('<?php echo $filePath; ?>', '_blank')">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 <?php echo $ic; ?> shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                        </svg>
                                                        <div class="min-w-0">
                                                            <p class="font-medium text-slate-700 truncate"><?php echo $fileName; ?></p>
                                                            <p class="text-xs text-blue-500 hover:underline mt-0.5">Click to view document</p>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo $filePath; ?>" download class="shrink-0 text-slate-400 hover:text-[#0033A1] transition-colors p-2" title="Download file" onclick="event.stopPropagation();">
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
                                            No file attached to this document.
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Tracking History -->
                <div class="lg:col-span-1 space-y-6">
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
                                    <?php foreach ($trackingHistory as $index => $history): ?>
                                        <?php
                                        $isLatest    = ($index === 0);
                                        $dotColor    = $isLatest ? 'bg-[#0033A1]' : 'bg-slate-400';
                                        $ringColor   = $isLatest ? 'bg-blue-100' : 'bg-slate-100';
                                        $actionName  = htmlspecialchars($history['action_taken'] ?? 'System Update');
                                        $remarks     = htmlspecialchars($history['remarks'] ?? '');
                                        $processedBy = htmlspecialchars($history['processed_by_name'] ?? '');
                                        $roleName    = htmlspecialchars($history['role_name'] ?? 'System');
                                        $dateFmt     = isset($history['created_at']) ? date('M d, Y - h:i A', strtotime($history['created_at'])) : 'Unknown Date';
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
                                                <?php echo $dateFmt; ?>
                                                <?php if (!empty($processedBy)): ?>
                                                    <span class="mx-1">•</span> By: <span class="font-medium text-slate-700"><?php echo $processedBy; ?></span> <span class="italic">(<?php echo $roleName; ?>)</span>
                                                <?php endif; ?>
                                            </time>
                                            <?php if (!empty($remarks)): ?>
                                                <p class="text-sm font-normal text-slate-600 bg-slate-50 p-2 rounded border border-slate-100 mt-2">"<?php echo $remarks; ?>"</p>
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

<script src="/PangasinanLIS/src/js/page_transition.js"></script>

</body>
</html>
