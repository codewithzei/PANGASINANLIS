<?php
session_start();
include '../../includes/db.php';
// Tatawagin natin yung fetcher na ginawa natin kanina para makuha yung details
include '../../includes/fetch_sp_received_documents_data.php'; 
include '../../components/header.php';

$userId = $_SESSION['user_id'] ?? null;
// Kunin ang ID sa URL (ex: process_document.php?id=5)
$documentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

if (!$documentId) {
    $_SESSION['route_status'] = 'error';
    $_SESSION['route_message'] = 'Invalid document ID.';
    header('Location: received_documents');
    exit;
}

// 1. KUNIN ANG DOCUMENT DETAILS (Gagamitin natin yung ginawa mo kanina)
$doc = getReceivedDocumentById($documentId, $userId);

// Kapag walang nakuha, ibig sabihin hindi sa kanya naka-assign o tapos na. I-kick out natin.
if (!$doc) {
    $_SESSION['route_status'] = 'error';
    $_SESSION['route_message'] = 'Document not found or you are not authorized to process it.';
    header('Location: received_documents');
    exit;
}

// 2. KUNIN ANG ROUTING OPTIONS PARA SA DROPDOWN 
$stmt = $pdo->prepare("
    SELECT routing_option_id AS id, routing_option_name 
    FROM routing_options 
    WHERE is_deleted = FALSE 
    AND status = 'active'
    AND routing_option_id IN (2,4)
    ORDER BY routing_option_id ASC
");
$stmt->execute();
$routingOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. KUNIN ANG TRACKING HISTORY MULA SA TOTOONG DATABASE (With Role Name)
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
    // Sinasalo natin ang error dito
    $historyError = $e->getMessage();
}
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">
        <?php
        $pageTitle = 'Process Document: ' . htmlspecialchars($doc['tracking_number']);
        include '../../components/topbar.php';
        ?>

        <main class="p-4 sm:p-6 w-full"> 
            
            <!-- 2-COLUMN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: VIEW DETAILS (60% Width) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        
                        <!-- HEADER WITH BACK BUTTON -->
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0 bg-slate-50">
                            <div class="flex items-center gap-3">
                                <!-- Back Arrow Icon -->
                                <a href="received_documents" class="p-1.5 text-slate-400 hover:text-[#0033A1] hover:bg-blue-50 rounded-lg transition-all" title="Back to Ongoing Tasks">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                                <h2 class="text-xl font-semibold text-gray-800">Document Information</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                
                                <!-- Tracking No -->
                                <div class="col-span-1 sm:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100 flex items-center justify-between gap-4">
                                    <span class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Tracking Number</span>
                                    <span class="text-2xl font-black text-[#0033A1] tracking-wide"><?php echo htmlspecialchars($doc['tracking_number']); ?></span>
                                </div>

                                <!-- Current Status -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Current Status</label>
                                    <span class="px-3 py-1 inline-flex bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full border border-yellow-200 shadow-sm uppercase">
                                        <?php echo htmlspecialchars($doc['status_name'] ?? 'PENDING PROCESSING'); ?>
                                    </span>
                                </div>

                                <!-- Date Received -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date Received</label>
                                    <p class="text-slate-800 font-medium">
                                        <?php 
                                            echo isset($doc['updated_at']) 
                                                ? date('F d, Y - h:i A', strtotime($doc['updated_at'])) 
                                                : 'N/A'; 
                                        ?>
                                    </p>
                                </div>

                                <!-- Subject Matter -->
                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Subject Matter</label>
                                    <div class="p-4 bg-slate-50 rounded border border-slate-200 text-slate-800 font-medium whitespace-pre-wrap"><?php echo htmlspecialchars($doc['subject_matter']); ?></div>
                                </div>

                                <!-- Document Type -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Document Type</label>
                                    <p class="text-slate-800 font-medium"><?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?></p>
                                </div>

                                <!-- Source / Origin -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Source / Origin</label>
                                    <p class="text-slate-800 font-medium">
                                        <?php 
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
                                            
                                            echo htmlspecialchars($mainSource . $location);
                                        ?>
                                    </p>
                                </div>

                                <!-- ATTACHMENTS SECTION -->
                                <div class="col-span-1 sm:col-span-2 pt-4 border-t border-slate-100">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Attachments</label>
                                    
                                    <?php if (!empty($doc['attachments'])): ?>
                                        <ul class="flex flex-col gap-2">
                                            <?php foreach ($doc['attachments'] as $file): 
                                                $filePath = '../../' . htmlspecialchars($file['file_path']); 
                                                $fileName = htmlspecialchars($file['file_name']);
                                                
                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                
                                                if ($ext === 'pdf') {
                                                    $iconClass = 'text-red-500';
                                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />';
                                                } elseif (in_array($ext, ['doc', 'docx'])) {
                                                    $iconClass = 'text-indigo-500';
                                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />';
                                                } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                                    $iconClass = 'text-green-600';
                                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0118 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0118 9.375v1.5m1.5-3.75C19.496 8.25 20 8.754 20 9.375v6.75C20 16.996 19.496 17.5 18.875 17.5m-12.75 0h12.75m-12.75 0C5.504 17.5 5 16.996 5 16.375v-6.75C5 8.754 5.504 8.25 6.125 8.25" />';
                                                } else {
                                                    $iconClass = 'text-blue-500';
                                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />';
                                                }
                                            ?>
                                                <li class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-sm transition-colors hover:bg-blue-50 hover:border-blue-200 cursor-pointer" onclick="window.open('<?php echo $filePath; ?>', '_blank')">
                                                    
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 <?php echo $iconClass; ?> shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <?php echo $iconPath; ?>
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

                <!-- RIGHT COLUMN: PROCESS FORM & TRACKING HISTORY (40% Width) -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- 1. ROUTE DOCUMENT CARD -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-slate-50 rounded-t-xl shrink-0">
                            <h2 class="text-lg font-semibold text-gray-800">Route Document</h2>
                        </div>
                        
                        <form action="../../includes/actions_sp_secretary/process_route_document" method="POST" class="p-6">
                            <input type="hidden" name="document_id" value="<?php echo $doc['document_id']; ?>">
                            <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($doc['tracking_number']); ?>">

                            <div class="space-y-5">
                                <!-- Action Selection -->
                                <div>
                                    <label for="routingOption" class="block text-sm font-medium text-gray-700 mb-1">Routing Option *</label>
                                    
                                    <?php 
                                        $isCommunication = stripos($doc['document_type_name'] ?? '', 'communication') !== false;
                                    ?>
                                    
                                    <select id="routingOption" name="routingOption"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                                            required>
                                        <option value="" disabled selected>Select where to route</option>
                                        
                                        <?php if (!isset($routingOptions['error'])): ?>
                                            <?php foreach ($routingOptions as $option): ?>
                                                <?php 
                                                    $isNotedOption = stripos($option['routing_option_name'], 'noted') !== false;
                                                    
                                                    if ($isNotedOption && !$isCommunication) {
                                                        continue;
                                                    }
                                                ?>
                                                <option value="<?= htmlspecialchars($option['id']) ?>" data-name="<?= htmlspecialchars(strtolower($option['routing_option_name'])) ?>">
                                                    <?= htmlspecialchars($option['routing_option_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Remarks / Instructions -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Remarks/Notes</label>
                                    <textarea id="remarks" name="remarks" placeholder="Enter additional remarks or notes"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                                style="resize: vertical; overflow: auto; min-height: 125px; max-height: 125px; width: 100%;"></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-2 border-t border-slate-200">
                                    <button type="submit" id="submitRouteBtn" name="submit_route" class="w-full bg-[#0033A1] hover:bg-blue-800 text-white font-bold py-3 px-4 rounded transition-colors flex items-center justify-center gap-2 shadow-sm">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Submit Document
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2. TRACKING HISTORY CARD (Now Dynamic!) -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between p-5 border-b border-gray-200 bg-slate-50 rounded-t-xl shrink-0">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                Tracking History
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            
                            <!-- I-display ang error dito kapag may mali sa SQL para alam agad natin -->
                            <?php if ($historyError): ?>
                                <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                                    <strong>Database Error:</strong> <?php echo htmlspecialchars($historyError); ?>
                                    <br><span class="text-xs text-red-600">Paki-check kung may 'first_name' at 'last_name' na column ang 'user_accounts' table mo.</span>
                                </div>
                            <?php endif; ?>

                            <!-- Timeline Container -->
                            <ol class="relative border-l-2 border-blue-200 ml-2.5">                  
                                
                                <?php if (empty($trackingHistory) && !$historyError): ?>
                                    <li class="mb-6 ml-6">
                                        <p class="text-sm font-normal text-slate-500 italic">No tracking history available yet.</p>
                                    </li>
                                <?php elseif (!empty($trackingHistory)): ?>
                                    
                                    <!-- I-loop ang laman ng document_history table -->
                                    <?php foreach ($trackingHistory as $index => $history): ?>
                                        <?php 
                                            $isLatest = ($index === 0); 
                                            
                                            $dotColor = $isLatest ? 'bg-[#0033A1]' : 'bg-slate-400';
                                            $ringColor = $isLatest ? 'bg-blue-100' : 'bg-slate-100';
                                            
                                            $actionName = htmlspecialchars($history['action_taken'] ?? 'System Update');
                                            $remarks = htmlspecialchars($history['remarks'] ?? '');
                                            $processedBy = htmlspecialchars($history['processed_by_name'] ?? '');
                                            // Kukunin natin yung role name, kung wala, default ay 'System'
                                            $roleName = htmlspecialchars($history['role_name'] ?? 'System');
                                            $dateFormatted = isset($history['created_at']) ? date('M d, Y - h:i A', strtotime($history['created_at'])) : 'Unknown Date';
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
                                                <?php echo $dateFormatted; ?> 
                                                <?php if (!empty($processedBy)): ?>
                                                    <span class="mx-1">•</span> Processed by: <span class="font-medium text-slate-700"><?php echo $processedBy; ?></span> <span class="italic">(<?php echo $roleName; ?>)</span>
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
<script>
    function toggleRouting(action) {
        const destDiv = document.getElementById('destination_div');
        const destSelect = document.getElementById('forward_to_routing_id');
        
        if (action === 'FORWARD') {
            destDiv.classList.remove('hidden');
            destSelect.setAttribute('required', 'required');
        } else {
            destDiv.classList.add('hidden');
            destSelect.removeAttribute('required');
            destSelect.value = ''; 
        }
    }
</script>

</body>
</html>