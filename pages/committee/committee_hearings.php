<?php
session_start();
include '../../includes/fetch_committee_hearings_data.php'; // I-include ang bagong file
include '../../components/header.php';
require_once '../../includes/badges.php';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

// FETCH LAHAT NG TABS DITO:
$allHearings       = getAllHearings($userId);
$scheduledHearings = getScheduledHearings($userId);
$approvedHearings  = getApprovedHearings($userId);
$deferredHearings  = getDeferredHearings($userId);
$remandedHearings  = getRemandedHearings($userId);
$withdrawnHearings = getWithdrawnHearings($userId);
?>
<style>
    /* Custom class to hide the scrollbar */
    .hide-scrollbar::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    .hide-scrollbar {
        -ms-overflow-style: none;  /* For IE and Edge */
        scrollbar-width: none;  /* For Firefox */
    }
</style>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'Committee Hearings';
        include '../../components/topbar.php';
        ?>

        <main class="p-3 sm:p-6">
            <!-- Global Message Display -->
            <?php if (isset($_SESSION['route_status'])): ?>
                <div id="globalMessage"
                    class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 w-max max-w-md animate-fade-in shadow-lg rounded-lg"
                    style="<?php echo $_SESSION['route_status'] === 'success' ? 'background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);' : 'background-color: rgb(254, 226, 226); color: rgb(127, 29, 29); border: 1px solid rgb(252, 165, 165);'; ?>">
                    <div class="flex items-center gap-3 px-6 py-4">
                        <?php if ($_SESSION['route_status'] === 'success'): ?>
                            <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        <?php endif; ?>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['route_message']); ?></span>
                        <button onclick="closeMessage()" class="text-current opacity-70 hover:opacity-100 transition ml-2">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php
                unset($_SESSION['route_status']);
                unset($_SESSION['route_message']);
                ?>
            <?php endif; ?>

            <!-- TABS NAVIGATION -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-2">
                <div class="p-4 border-b border-slate-200">
                    <div class="flex space-x-1 overflow-x-auto flex-nowrap pb-1 hide-scrollbar">
                        <button class="tab-button active shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="tab-all-hearings" onclick="switchTab(this)">
                            All Hearings
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-scheduled" onclick="switchTab(this)">
                            Scheduled
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-approved" onclick="switchTab(this)">
                            Approved
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-deferred" onclick="switchTab(this)">
                            Deferred
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-remanded" onclick="switchTab(this)">
                            Remanded
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-withdrawn" onclick="switchTab(this)">
                            Withdrawn
                        </button>
                    </div>
                </div>
            </div>

            <!-- =====================================
                 TAB 1: ALL HEARINGS
            ====================================== -->
            <div id="tab-all-hearings" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">All Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage all legislative documents for hearings</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-all-hearings" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-all-hearings', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <!-- BAGO: min-w-max para auto-adjust ang width base sa content, at hihinga nang tama -->
                    <table id="table-all-hearings" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(0, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: Fixed width range para sa Subject Matter -->
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(2, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(3, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-all-hearings')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-all-hearings')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: pr-8 para hindi dikit sa pader ang action -->
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-all-hearings">
                            <!-- PHP LOOP DITO PARA SA ALL HEARINGS -->
                            <?php if (isset($allHearings['error'])): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($allHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($allHearings)): ?>
                                <?php foreach ($allHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php
                                            $sn = $doc['status_name'] ?? '';
                                            $did = (int) $doc['document_id'];
                                            if ($sn === 'On Going'): ?>
                                                <button type="button" onclick="processHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-green-700 bg-green-100 hover:bg-green-200 rounded transition duration-150 cursor-pointer" title="Process Hearing Outcome">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                    </svg>
                                                    Process
                                                </button>
                                            <?php else: ?>
                                                <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    View
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-all-hearings" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-all-hearings" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 2: SCHEDULED
            ====================================== -->
            <div id="tab-scheduled" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Scheduled Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">View documents currently scheduled for hearings</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-scheduled" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-scheduled', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <!-- BAGO: min-w-max para auto-adjust ang width base sa content, at hihinga nang tama -->
                    <table id="table-scheduled" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(0, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: Fixed width range para sa Subject Matter -->
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(2, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(3, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-scheduled')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-scheduled')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: pr-8 para hindi dikit sa pader ang action -->
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-scheduled">
                            <!-- PHP LOOP DITO PARA SA SCHEDULED HEARINGS -->
                            <?php if (isset($scheduledHearings['error'])): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($scheduledHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($scheduledHearings)): ?>
                                <?php foreach ($scheduledHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php
                                            $sn = $doc['status_name'] ?? '';
                                            $did = (int) $doc['document_id'];
                                            if ($sn === 'On Going'): ?>
                                                <button type="button" onclick="processHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-green-700 bg-green-100 hover:bg-green-200 rounded transition duration-150 cursor-pointer" title="Process Hearing Outcome">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                    </svg>
                                                    Process
                                                </button>
                                            <?php else: ?>
                                                <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    View
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-scheduled" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-scheduled" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 3: APPROVED
            ====================================== -->
            <div id="tab-approved" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Approved Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">View documents with approved committee reports</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-approved" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-approved', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <table id="table-approved" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <!-- BAGO: Select All Checkbox (BLUE NA) -->
                                <th scope="col" class="px-6 py-4 w-12 text-center">
                                    <input type="checkbox" id="selectAllApproved" onclick="toggleAllApproved(this)" class="w-4 h-4 text-[#0033A1] bg-white border-gray-300 rounded focus:ring-[#0033A1] cursor-pointer">
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-approved')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(2, 'table-approved')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(3, 'table-approved')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-approved')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-approved')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-approved')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(7, 'table-approved')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-approved">
                            <?php if (isset($approvedHearings['error'])): ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($approvedHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($approvedHearings)): ?>
                                <?php foreach ($approvedHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150 group" data-doc-id="<?php echo (int) $doc['document_id']; ?>">
                                        <!-- BAGO: Row Checkbox (BLUE NA) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <input type="checkbox" value="<?php echo (int) $doc['document_id']; ?>" class="approved-checkbox w-4 h-4 text-[#0033A1] bg-white border-gray-300 rounded focus:ring-[#0033A1] cursor-pointer" onchange="updateApprovedSelection()">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php $did = (int) $doc['document_id']; ?>
                                            <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View document">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                 <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500 italic">
                                        No approved hearings found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- BAGO: Action Banner (BLUE THEME) -->
                <div id="approvedActionBar" class="hidden mx-4 sm:mx-6 mt-4 mb-2 p-4 bg-blue-50 border border-blue-200 rounded-lg items-center justify-between shadow-sm transition-all duration-300">
                    <span class="text-[#0033A1] font-semibold text-sm">
                        <span id="approvedSelectedCount">0</span> document(s) selected
                    </span>
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="clearApprovedSelection()" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition-colors">
                            Clear Selection
                        </button>
                        <button type="button" onclick="processCommitteeReport()" class="bg-[#0033A1] hover:bg-blue-900 text-white px-4 py-2 rounded-lg font-bold text-sm flex items-center gap-2 shadow-sm transition-colors">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Create Committee Report
                        </button>
                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-approved" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-approved" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 4: DEFERRED
            ====================================== -->
            <div id="tab-deferred" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Deferred Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">View documents that were deferred during committee hearings</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-deferred" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-deferred', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <!-- BAGO: min-w-max para auto-adjust ang width base sa content, at hihinga nang tama -->
                    <table id="table-deferred" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(0, 'table-deferred')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-deferred')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: Fixed width range para sa Subject Matter -->
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(2, 'table-deferred')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(3, 'table-deferred')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-deferred')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-deferred')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-deferred')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: pr-8 para hindi dikit sa pader ang action -->
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-deferred">
                            <!-- PHP LOOP DITO PARA SA DEFERRED HEARINGS -->
                            <?php if (isset($deferredHearings['error'])): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($deferredHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($deferredHearings)): ?>
                                <?php foreach ($deferredHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php $did = (int) $doc['document_id']; ?>
                                            <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View document">
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
                    <div id="paginationInfo-deferred" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-deferred" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 5: REMANDED
            ====================================== -->
            <div id="tab-remanded" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Remanded Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">View documents remanded back to the committee</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-remanded" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-remanded', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <!-- BAGO: min-w-max para auto-adjust ang width base sa content, at hihinga nang tama -->
                    <table id="table-remanded" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(0, 'table-remanded')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-remanded')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: Fixed width range para sa Subject Matter -->
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(2, 'table-remanded')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(3, 'table-remanded')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-remanded')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-remanded')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-remanded')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: pr-8 para hindi dikit sa pader ang action -->
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-remanded">
                            <!-- PHP LOOP DITO PARA SA REMANDED HEARINGS -->
                            <?php if (isset($remandedHearings['error'])): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($remandedHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($remandedHearings)): ?>
                                <?php foreach ($remandedHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php $did = (int) $doc['document_id']; ?>
                                            <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View document">
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
                    <div id="paginationInfo-remanded" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-remanded" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 6: WITHDRAWN
            ====================================== -->
            <div id="tab-withdrawn" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display: none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Withdrawn Hearings</h2>
                            <p class="text-sm text-gray-600 mt-1">View documents withdrawn from the committee</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-withdrawn" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-withdrawn', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto w-full">
                    <!-- BAGO: min-w-max para auto-adjust ang width base sa content, at hihinga nang tama -->
                    <table id="table-withdrawn" class="w-full min-w-max divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(0, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Agenda No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(1, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: Fixed width range para sa Subject Matter -->
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer min-w-[300px] max-w-[500px]" onclick="sortTable(2, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(3, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Date
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(4, 'table-withdrawn')">
                                    <div class="flex items-center justify-center gap-2">Cycle
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(5, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Chairperson
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider cursor-pointer whitespace-nowrap" onclick="sortTable(6, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <!-- BAGO: pr-8 para hindi dikit sa pader ang action -->
                                <th scope="col" class="px-6 py-4 pr-8 text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-withdrawn">
                            <!-- PHP LOOP DITO PARA SA WITHDRAWN HEARINGS -->
                            <?php if (isset($withdrawnHearings['error'])): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($withdrawnHearings['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($withdrawnHearings)): ?>
                                <?php foreach ($withdrawnHearings as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_number'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]"><?php echo htmlspecialchars($doc['tracking_number']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700 min-w-[300px] max-w-[500px] whitespace-normal break-words"><?php echo htmlspecialchars($doc['subject_matter']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['agenda_date'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center font-medium"><?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($doc['chairperson'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($doc['status_name']) : 'bg-gray-100 text-gray-800'; ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 pr-8 whitespace-nowrap text-sm font-medium">
                                            <?php $did = (int) $doc['document_id']; ?>
                                            <button type="button" onclick="viewHearingDocument(<?php echo $did; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View document">
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
        document.getElementById(targetId).style.display = '';
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

    function processHearingDocument(documentId) {
        window.location.href = 'process_hearing_document?id=' + documentId;
    }

    function viewHearingDocument(documentId) {
        window.location.href = 'view_hearing_document?id=' + documentId;
    }

    // Para sa action button kapag viniew yung document
    function viewDocument(documentId) {
        console.log("Viewing document ID:", documentId);
        // Dito natin idadagdag yung AJAX logic para sa View Modal
        // openModal('view-document-modal'); 
    }

    // ============================================================
    // MULTI-SELECT LOGIC FOR APPROVED HEARINGS
    // ============================================================

    function updateApprovedSelection() {
        const checkboxes = document.querySelectorAll('.approved-checkbox');
        const selectAll = document.getElementById('selectAllApproved');
        const actionBar = document.getElementById('approvedActionBar');
        const countSpan = document.getElementById('approvedSelectedCount');

        let selectedCount = 0;
        let totalCount = checkboxes.length;

        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) {
                selectedCount++;
                // BAGO: Lagyan ng LIGHT BLUE na background yung row
                row.classList.add('bg-blue-50'); 
                row.classList.remove('bg-white', 'hover:bg-slate-50');
            } else {
                // Alisin yung background
                row.classList.remove('bg-blue-50');
                row.classList.add('hover:bg-slate-50');
            }
        });

        // Update Select All Checkbox state
        if (selectAll) {
            selectAll.checked = (selectedCount === totalCount && totalCount > 0);
            selectAll.indeterminate = (selectedCount > 0 && selectedCount < totalCount);
        }

        // Ipakita o itago yung floating Action Banner
        if (selectedCount > 0) {
            actionBar.classList.remove('hidden');
            actionBar.classList.add('flex');
            countSpan.textContent = selectedCount;
        } else {
            actionBar.classList.add('hidden');
            actionBar.classList.remove('flex');
        }
    }

    function toggleAllApproved(source) {
        const checkboxes = document.querySelectorAll('.approved-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
        });
        updateApprovedSelection();
    }

    function clearApprovedSelection() {
        const checkboxes = document.querySelectorAll('.approved-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        updateApprovedSelection();
    }

    // Initialize pagination when page loads
    document.addEventListener('DOMContentLoaded', function () {
        window.paginationManagers = {};

        // Added all 6 tabs
        const tabs = [
            { tableBodyId: 'tableBody-all-hearings', paginationContainerId: 'paginationContainer-all-hearings', infoDisplayId: 'paginationInfo-all-hearings' },
            { tableBodyId: 'tableBody-scheduled', paginationContainerId: 'paginationContainer-scheduled', infoDisplayId: 'paginationInfo-scheduled' },
            { tableBodyId: 'tableBody-approved', paginationContainerId: 'paginationContainer-approved', infoDisplayId: 'paginationInfo-approved' },
            { tableBodyId: 'tableBody-deferred', paginationContainerId: 'paginationContainer-deferred', infoDisplayId: 'paginationInfo-deferred' },
            { tableBodyId: 'tableBody-remanded', paginationContainerId: 'paginationContainer-remanded', infoDisplayId: 'paginationInfo-remanded' },
            { tableBodyId: 'tableBody-withdrawn', paginationContainerId: 'paginationContainer-withdrawn', infoDisplayId: 'paginationInfo-withdrawn' }
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
                        title: "No documents found",
                        subtitle: "There's nothing to display here.",
                    });
                }
            }
        });
    });
</script>

<?php include 'modals/create_committee_reports.php'; ?>

</body>
</html>