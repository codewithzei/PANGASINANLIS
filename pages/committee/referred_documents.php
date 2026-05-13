<?php
session_start();
include '../../components/header.php';
include '../../includes/fetch_referred_documents_data.php';
include '../../includes/fetch_for_opinion_data.php'; // BAGO: I-include ito
include '../../includes/fetch_referred_withdrawn_documents_data.php';
require_once '../../includes/badges.php';

$userId = $_SESSION['user_id'] ?? null;
$referredDocs = [];
$opinionDocs = []; // BAGO
$agendaDocs = [];
$withdrawnDocs = [];

if ($userId) {
    $referredDocs = getReferredDocuments($userId);
    $opinionDocs = getForOpinionDocuments($userId); // BAGO: Tawagin ang bagong function
    $agendaDocs = getReadyForAgendaDocuments($userId);
    $withdrawnDocs = getReferredWithdrawnDocuments($userId);
}

$tabCountReferred = (is_array($referredDocs) && !isset($referredDocs['error'])) ? count($referredDocs) : 0;
$tabCountOpinion = (is_array($opinionDocs) && !isset($opinionDocs['error'])) ? count($opinionDocs) : 0;
$tabCountAgenda = (is_array($agendaDocs) && !isset($agendaDocs['error'])) ? count($agendaDocs) : 0;
$tabCountWithdrawn = (is_array($withdrawnDocs) && !isset($withdrawnDocs['error'])) ? count($withdrawnDocs) : 0;

?>
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'Referred Documents';
        include '../../components/topbar.php';
        ?>

        <main class="p-3 sm:p-6">
            <!-- Flash messages are handled by the JS showEndorseToast() in the endorse_document modal -->

            <!-- TABS NAVIGATION -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-2">
                <div class="p-4 border-b border-slate-200">
                    <div class="flex space-x-1 overflow-x-auto flex-nowrap pb-1 hide-scrollbar">
                        <button class="tab-button active shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 inline-flex items-center gap-1.5" data-tab="tab-referred" onclick="switchTab(this)">
                            <span>Referred</span>
                            <span class="tab-count-badge inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800"><?php echo (int) $tabCountReferred; ?></span>
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent inline-flex items-center gap-1.5" data-tab="tab-for-opinion" onclick="switchTab(this)">
                            <span>For Opinion</span>
                            <span class="tab-count-badge inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700"><?php echo (int) $tabCountOpinion; ?></span>
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent inline-flex items-center gap-1.5" data-tab="tab-ready-for-agenda" onclick="switchTab(this)">
                            <span>Ready for Agenda</span>
                            <span class="tab-count-badge inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700"><?php echo (int) $tabCountAgenda; ?></span>
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent inline-flex items-center gap-1.5" data-tab="tab-withdrawn" onclick="switchTab(this)">
                            <span>Withdrawn</span>
                            <span class="tab-count-badge inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700"><?php echo (int) $tabCountWithdrawn; ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- =====================================
                 TAB 1: REFERRED
            ====================================== -->
            <div id="tab-referred" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Referred Documents</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage all legislative documents referred to the committee</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-referred', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <!-- BINALIK NATIN SA min-w-full para mag-fit ng kusa at nag-remove ng table-fixed w-full -->
                    <table id="table-referred" class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <!-- Tinanggal yung mga w-[15%], w-[25%] at nilagyan ng whitespace-nowrap ang lahat ng headers -->
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(0, 'table-referred')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(1, 'table-referred')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(2, 'table-referred')">
                                    <div class="flex items-center gap-2">Document Type & Source
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(3, 'table-referred')">
                                    <div class="flex items-center gap-2">Date Referred
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(4, 'table-referred')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(5, 'table-referred')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-referred">
                            <?php if (isset($referredDocs['error'])): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-red-600 font-medium">
                                        <?php echo htmlspecialchars($referredDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($referredDocs)): ?>
                                <?php foreach ($referredDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <!-- 1. Tracking No -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        
                                        <!-- 2. Subject Matter: HINDI naka-nowrap para mag-break sa desktop, pero may min-w-64 (256px) para maayos sa mobile -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 break-words min-w-64">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        
                                        <!-- 3. Document Type & Source -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div>
                                                <span class="font-medium"><?php echo htmlspecialchars($doc['document_type_name']); ?></span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <?php echo htmlspecialchars($doc['source_display']); ?>
                                            </div>
                                        </td>
                                        
                                        <!-- 4. Date Referred -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php 
                                                $date = new DateTime($doc['date_referred']);
                                                echo $date->format('M d, Y'); 
                                            ?>
                                            <br>
                                            <span class="text-xs text-gray-400">
                                                <?php echo $date->format('h:i A'); ?>
                                            </span>
                                        </td>
                                        
                                        <!-- 5. Status -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php 
                                                $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; 
                                            ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>

                                        <!-- 6. Cycle / Version -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700 text-center font-bold">
                                            <?php echo htmlspecialchars($doc['cycle']); ?>
                                        </td>
                                        
                                        <!-- 7. Action -->
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <button onclick="openEndorseModal(<?php echo $doc['document_id']; ?>, '<?php echo htmlspecialchars($doc['tracking_number'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($doc['subject_matter'], ENT_QUOTES); ?>')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-yellow-700 bg-yellow-100 hover:bg-yellow-200 rounded transition duration-150 cursor-pointer" title="Endorse Document">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/>
                                                    <path d="m21.854 2.147-10.94 10.939"/>
                                                </svg>
                                                Endorse
                                            </button>
                                            <button onclick="viewDocument(<?php echo $doc['document_id']; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-referred" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-referred" class="flex items-center space-x-2"></div>
                </div>
            </div>


            <div id="tab-for-opinion" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
    
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Opinion Workflow</h2>
                            <p class="text-sm text-gray-600 mt-1">Monitor and manage opinion workflow across different offices</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput-opinion" placeholder="Search tracking number or office..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterOpinionWorkflowCards(this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="forOpinionCardsRoot" class="p-6 flex flex-col gap-6 bg-slate-50/50">

                    <?php if (isset($opinionDocs['error'])): ?>
                        <div class="for-opinion-error text-center py-8">
                            <p class="text-sm text-red-600 font-medium"><?php echo htmlspecialchars($opinionDocs['error']); ?></p>
                        </div>
                    <?php elseif (!empty($opinionDocs)): ?>
                        <?php foreach ($opinionDocs as $doc): ?>
                            <div class="workflow-card bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200">
                                
                                <div class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center gap-4">
                                    <div class="flex flex-row items-start gap-3 sm:gap-4 flex-1">
                                        <button onclick="toggleWorkflow(this)" class="flex-shrink-0 mt-0.5 w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50 cursor-pointer transition-colors">
                                            <svg class="caret-icon h-5 w-5 transition-transform duration-300 rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        
                                        <div class="flex flex-col gap-1 w-full min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-base font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></h3>
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-[#EBEBFF] text-[#4F46E5] tracking-wide border border-transparent">
                                                    <?php echo htmlspecialchars($doc['status_name']); ?>
                                                </span>
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-slate-100 text-gray-700 border border-transparent">
                                                    Cycle <?php echo htmlspecialchars($doc['cycle']); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700 mt-0.5"><?php echo htmlspecialchars($doc['subject_matter']); ?></p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row items-center justify-between lg:justify-end gap-4 sm:gap-6 lg:ml-auto w-full lg:w-auto pl-11 sm:pl-12 lg:pl-0 mt-2 lg:mt-0">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col text-right">
                                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Progress</span>
                                                <span class="text-xs font-bold text-gray-900 leading-none"><?php echo $doc['favorable_count']; ?> / <?php echo $doc['total_offices']; ?> Favorable</span>
                                            </div>
                                            <div class="w-20 sm:w-20 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-indigo-500 rounded-full" style="width: <?php echo $doc['progress_percentage']; ?>%"></div>
                                            </div>
                                        </div>

                                        <?php if (!empty($doc['needs_resolve'])): ?>
                                            <button type="button" onclick='openResolveModal(<?php echo (int) $doc['document_id']; ?>, <?php echo json_encode($doc['tracking_number'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($doc['subject_matter'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>)' class="order-first lg:order-0 text-sm text-amber-700 bg-amber-100 hover:bg-amber-200 border border-yellow-500 flex items-center gap-1.5 font-semibold px-3 py-1.5 rounded-lg shadow-sm transition-colors shrink-0 cursor-pointer">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Resolve
                                            </button>
                                        <?php endif; ?>

                                        <button type="button" onclick="viewOpinionDocument(<?php echo $doc['document_id']; ?>)" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1.5 font-semibold px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors shrink-0 cursor-pointer">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </button>

                                        
                                    </div>
                                </div>

                                <div class="workflow-content border-t border-slate-200 bg-white">
                                    <div class="w-full overflow-x-auto">
                                        <table class="min-w-full divide-y divide-slate-200 text-sm text-gray-700">
                                            <thead class="bg-slate-50 border-b border-slate-200">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Office</th>
                                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Status</th>
                                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Latest Action</th>
                                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-slate-100">
                                                
                                                <?php foreach ($doc['endorsements'] as $office): 
                                                    $round = $office['endorsement_round'] ?? 1;

                                                    // Badge color conditioning
                                                    $badgeClass = "bg-blue-100 text-blue-800";
                                                    if ($office['opinion_status_id'] == 2) $badgeClass = "bg-green-100 text-green-800";
                                                    if ($office['opinion_status_id'] == 3) $badgeClass = "bg-amber-100 text-amber-700";
                                                    if ($office['opinion_status_id'] == 4) $badgeClass = "bg-cyan-100 text-cyan-800";
                                                ?>
                                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                                        <td class="px-6 py-4 text-gray-800 break-words min-w-56">
                                                            <?php echo htmlspecialchars($office['opinion_office_name']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full <?php echo $badgeClass; ?>">
                                                                <?php echo strtoupper(htmlspecialchars($office['opinion_status_name'])); ?>
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 text-gray-500 break-words min-w-56">
                                                            <?php echo htmlspecialchars($office['latest_action']); ?>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                
                                                            <?php if ($office['opinion_status_id'] == 1 || $office['opinion_status_id'] == 4): ?>
                                                                <button onclick='openOpinionModal(<?php echo (int) $doc['document_id']; ?>, <?php echo (int) $office['endorsement_id']; ?>, <?php echo json_encode($office['opinion_office_name'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>)' class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded transition duration-150 cursor-pointer">
                                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                                                    Upload Opinion
                                                                </button>
                                                            
                                                            <?php elseif ($office['opinion_status_id'] == 3): ?>
                                                                
                                                                <?php if ($round >= 2 && !empty($office['compliance_description'])): ?>
                                                                    <span class="text-xs font-bold text-gray-500 italic bg-gray-100 border border-gray-200 px-3 py-1.5 rounded">
                                                                        Final Unfavorable
                                                                    </span>
                                                                <?php else: ?>
                                                                    <button onclick='openComplianceModal(<?php echo (int) $doc['document_id']; ?>, <?php echo (int) $office['endorsement_id']; ?>, <?php echo json_encode($office['opinion_office_name'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>)' class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded transition duration-150 cursor-pointer">
                                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                                        Upload Compliance
                                                                    </button>
                                                                <?php endif; ?>
                                                                
                                                            <?php endif; ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-for-opinion" class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?php echo !empty($opinionDocs) ? '1' : '0'; ?></span> to <span class="font-medium"><?php echo count($opinionDocs); ?></span> of <span class="font-medium"><?php echo count($opinionDocs); ?></span> results
                    </div>
                    <div id="paginationContainer-for-opinion" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 3: READY FOR AGENDA (For Calendar)
            ====================================== -->
            <div id="tab-ready-for-agenda" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Ready for Agenda</h2>
                            <p class="text-sm text-gray-600 mt-1">Documents with favorable opinions (or resolved mixed opinions). Schedule committee agenda details.</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput-agenda" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-ready-for-agenda', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table id="table-ready-for-agenda" class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Tracking No.</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Subject Matter</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Document Type &amp; Source</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Date Referred</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Cycle</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-ready-for-agenda">
                            <?php if (isset($agendaDocs['error'])): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-red-600 font-medium"><?php echo htmlspecialchars($agendaDocs['error']); ?></td>
                                </tr>
                            <?php elseif (empty($agendaDocs)): ?>
                                <tr class="empty-state">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">No documents ready for agenda.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($agendaDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 break-words min-w-64"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div><span class="font-medium"><?php echo htmlspecialchars($doc['document_type_name'] ?? ''); ?></span></div>
                                            <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($doc['source_display'] ?? ''); ?></div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php
                                            $date = new DateTime($doc['date_referred']);
                                            echo $date->format('M d, Y');
                                            ?><br><span class="text-xs text-gray-400"><?php echo $date->format('h:i A'); ?></span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php 
                                                $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; 
                                            ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700 text-center font-bold"><?php echo htmlspecialchars($doc['cycle']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <button type="button" onclick='openScheduleAgendaModal(<?php echo (int) $doc['document_id']; ?>, <?php echo json_encode($doc['tracking_number'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($doc['subject_matter'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>)' class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-100 hover:bg-blue-200 rounded transition duration-150 cursor-pointer" title="Schedule for Agenda">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                            Schedule</button>
                                            <button type="button" onclick="viewOpinionDocument(<?php echo (int) $doc['document_id']; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-ready-for-agenda" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-ready-for-agenda" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 3: Withdrawn (Withdrawn)
            ====================================== -->
            <div id="tab-withdrawn" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Withdrawn</h2>
                            <p class="text-sm text-gray-600 mt-1">Documents that have been withdrawn.</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput-withdrawn" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-withdrawn', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table id="table-withdrawn" class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Tracking No.</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Subject Matter</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Document Type &amp; Source</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Date Withdrawn</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Cycle</th>
                                <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-withdrawn">
                            <?php if (isset($withdrawnDocs['error'])): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-red-600 font-medium"><?php echo htmlspecialchars($withdrawnDocs['error']); ?></td>
                                </tr>
                            <?php elseif (!empty($withdrawnDocs)): ?>
                                <?php foreach ($withdrawnDocs  as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 break-words min-w-64"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div><span class="font-medium"><?php echo htmlspecialchars($doc['document_type_name'] ?? ''); ?></span></div>
                                            <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($doc['source_display'] ?? ''); ?></div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php
                                            $wd = $doc['date_withdrawn'] ?? $doc['updated_at'] ?? null;
                                            if ($wd) {
                                                $date = new DateTime($wd);
                                                echo $date->format('M d, Y');
                                                ?><br><span class="text-xs text-gray-400"><?php echo $date->format('h:i A'); ?></span><?php
                                            } else {
                                                echo '—';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php 
                                                $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; 
                                            ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700 text-center font-bold"><?php echo htmlspecialchars($doc['cycle']); ?></td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-center">
                                            <button type="button" onclick="viewOpinionDocument(<?php echo (int) $doc['document_id']; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-withdrawn" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-withdrawn" class="flex items-center space-x-2"></div>
                </div>
            </div>
            

        </main>

    </div>
</div>

<?php include 'modals/schedule_agenda.php'; ?>
<?php include 'modals/resolve_opinion_document.php'; ?>
<?php include 'modals/endorse_document.php'; ?>
<?php include 'modals/upload_opinion.php'; ?>
<?php include 'modals/upload_compliance.php'; ?>
<?php include 'modals/view_opinion_document.php'; ?>

<script src="/PangasinanLIS/src/js/global.js"></script>
<script src="/PangasinanLIS/src/js/page_transition.js"></script>
<script>
    function switchTab(btn) {
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-button').forEach(function (b) {
            b.classList.remove('active', 'text-blue-600', 'border-blue-600');
            b.classList.add('text-slate-600', 'border-transparent');
        });

        // Activate clicked button
        btn.classList.add('active', 'text-blue-600', 'border-blue-600');
        btn.classList.remove('text-slate-600', 'border-transparent');

        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(function (panel) {
            panel.style.display = 'none';
        });

        // Show the target panel
        const targetId = btn.getAttribute('data-tab');
        const panel = document.getElementById(targetId);
        if (panel) {
            panel.style.display = '';
        }

        // Active tab: blue count pill; others: slate
        document.querySelectorAll('.tab-button .tab-count-badge').forEach(function (el) {
            el.classList.remove('bg-blue-100', 'text-blue-800');
            el.classList.add('bg-slate-100', 'text-slate-700');
        });
        const activeBadge = btn.querySelector('.tab-count-badge');
        if (activeBadge) {
            activeBadge.classList.remove('bg-slate-100', 'text-slate-700');
            activeBadge.classList.add('bg-blue-100', 'text-blue-800');
        }
    }

    // Filter helper for tab-specific search inputs
    function filterTabTable(tableBodyId, query) {
        const tbody = document.getElementById(tableBodyId);
        if (!tbody) return;
        const rows = tbody.querySelectorAll('tr:not(.empty-state)');
        const q = query.toLowerCase();
        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
        // Re-run pagination for this tab
        const mgr = window.paginationManagers && window.paginationManagers[tableBodyId];
        if (mgr) mgr.init();
    }

    // viewOpinionDocument() - navigates to full-page view
    function viewOpinionDocument(documentId) {
        window.location.href = 'view_opinion_document?id=' + documentId;
    }
    // viewDocument() for the Referred tab
    function viewDocument(documentId) {
        window.location.href = 'view_document?id=' + documentId;
    }

    function filterOpinionWorkflowCards(query) {
        const q = String(query).toLowerCase().trim();
        document.querySelectorAll('#tab-for-opinion .workflow-card').forEach(function (card) {
            const text = card.textContent.toLowerCase();
            card.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
    }

    function toggleWorkflow(button) {
        // Kunin yung buong main card kung saan pinindot yung button
        const card = button.closest('.workflow-card');
        
        // Hanapin yung nested table (content) at yung caret icon sa loob ng card na 'yon
        const content = card.querySelector('.workflow-content');
        const caret = card.querySelector('.caret-icon');
        
        // Toggle the 'hidden' class para lumitaw/mawala yung content
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            caret.classList.add('rotate-180'); // Iikot yung arrow pataas
        } else {
            content.classList.add('hidden');
            caret.classList.remove('rotate-180'); // Babalik yung arrow pababa
        }
    }

    // Initialize pagination when page loads
    document.addEventListener('DOMContentLoaded', function () {
        window.paginationManagers = {};

        const tabs = [
            { tableBodyId: 'tableBody-referred', paginationContainerId: 'paginationContainer-referred', infoDisplayId: 'paginationInfo-referred' },
            { tableBodyId: 'tableBody-ready-for-agenda', paginationContainerId: 'paginationContainer-ready-for-agenda', infoDisplayId: 'paginationInfo-ready-for-agenda' },
            { tableBodyId: 'tableBody-withdrawn', paginationContainerId: 'paginationContainer-withdrawn', infoDisplayId: 'paginationInfo-withdrawn', emptyStateTitle: 'No documents withdrawn', emptyStateSubtitle: "There's nothing to display here." }
        ];

        tabs.forEach(function(cfg) {
            if (typeof PaginationManager !== 'undefined') {
                const mgr = new PaginationManager({
                    itemsPerPage: 10,
                    tableBodyId: cfg.tableBodyId,
                    paginationContainerId: cfg.paginationContainerId,
                    infoDisplayId: cfg.infoDisplayId
                });
                mgr.init();
                window.paginationManagers[cfg.tableBodyId] = mgr;
            }

            // Show empty state if no data
            const tbody = document.getElementById(cfg.tableBodyId);
            if (tbody) {
                const rows = tbody.querySelectorAll('tr:not(.empty-state)');
                if (rows.length === 0 && typeof toggleEmptyState === 'function') {
                    toggleEmptyState(cfg.tableBodyId, true, {
                        title: cfg.emptyStateTitle || 'No documents found',
                        subtitle: cfg.emptyStateSubtitle || "There's nothing to display here.",
                    });
                }
            }
        });

        const forOpinionRoot = document.getElementById('forOpinionCardsRoot');
        if (forOpinionRoot && !forOpinionRoot.querySelector('.for-opinion-error') && !forOpinionRoot.querySelector('.workflow-card') && typeof toggleEmptyStateContainer === 'function') {
            toggleEmptyStateContainer('forOpinionCardsRoot', true, {
                title: 'No documents currently for opinion',
                subtitle: "There's nothing to display here.",
            });
        }
    });
</script>

</body>
</html>