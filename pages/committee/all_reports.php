<?php
session_start();
include '../../components/header.php';
?>
<style>
    /* Custom class para itago ang scrollbar */
    .hide-scrollbar::-webkit-scrollbar {
        display: none; /* Para sa Chrome, Safari at Opera */
    }
    .hide-scrollbar {
        -ms-overflow-style: none;  /* Para sa IE at Edge */
        scrollbar-width: none;  /* Para sa Firefox */
    }
</style>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'All Reports';
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
                    <!-- Idinagdag: overflow-x-auto, flex-nowrap -->
                    <!-- Tip: Pwede mong lagyan ng 'scrollbar-hide' class kung may plugin ka, o hayaan lang para sa default scrollbar -->
                    <div class="flex space-x-1 overflow-x-auto flex-nowrap pb-1 hide-scrollbar">
                        
                        <!-- Idinagdag sa bawat button: shrink-0 at whitespace-nowrap -->
                        <button class="tab-button active shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="tab-all-reports" onclick="switchTab(this)">
                            All Reports
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-committee-reports" onclick="switchTab(this)">
                            Committee Reports
                        </button>
                        <button class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 border-b-2 border-transparent" data-tab="tab-joint-committee-reports" onclick="switchTab(this)">
                            Joint Committee Reports
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
                                <input type="text" id="searchInput-referred" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-referred', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <!-- table-fixed para pantay-pantay ang hatian -->
                    <table id="table-referred" class="min-w-full divide-y divide-slate-200 table-fixed w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(0, 'table-referred')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[25%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(1, 'table-referred')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[20%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(2, 'table-referred')">
                                    <div class="flex items-center gap-2">Document Type
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(3, 'table-referred')">
                                    <div class="flex items-center gap-2">Division
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(4, 'table-referred')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[10%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-referred">
                            <?php if (isset($ongoingDocs['error'])): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($ongoingDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($ongoingDocs)): ?>
                                <?php foreach ($ongoingDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 whitespace-normal wrap-break-word">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['division_name'] ?? 'Not Routed'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php $bgClass = getStatusBadgeClass($doc['status_name']); ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewDocument(<?php echo $doc['document_id']; ?>)" class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="View Document">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
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

            <!-- =====================================
                 TAB 2: COMPLETED
            ====================================== -->
            <div id="tab-completed" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display:none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Completed Documents</h2>
                            <p class="text-sm text-gray-600 mt-1">View completed and finalized documents</p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" id="searchInput-completed" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    oninput="filterTabTable('tableBody-completed', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="table-completed" class="min-w-full divide-y divide-slate-200 table-fixed w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(0, 'table-completed')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[25%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(1, 'table-completed')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[20%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(2, 'table-completed')">
                                    <div class="flex items-center gap-2">Document Type
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(3, 'table-completed')">
                                    <div class="flex items-center gap-2">Division
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(4, 'table-completed')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[10%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-completed">
                            <?php if (isset($completedDocs['error'])): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($completedDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($completedDocs)): ?>
                                <?php foreach ($completedDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 whitespace-normal wrap-break-word">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['division_name'] ?? 'Not Routed'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php $bgClass = getStatusBadgeClass($doc['status_name']); ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewDocument(<?php echo $doc['document_id']; ?>)" class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="View Document">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-completed" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-completed" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <!-- =====================================
                 TAB 3: WITHDRAWN
            ====================================== -->
            <div id="tab-withdrawn" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="display:none;">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Withdrawn Documents</h2>
                            <p class="text-sm text-gray-600 mt-1">View withdrawn and cancelled documents</p>
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

                <div class="overflow-x-auto">
                    <table id="table-withdrawn" class="min-w-full divide-y divide-slate-200 table-fixed w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(0, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[25%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(1, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[20%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(2, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Document Type
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(3, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Division
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[15%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer" onclick="sortTable(4, 'table-withdrawn')">
                                    <div class="flex items-center gap-2">Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="w-[10%] px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-withdrawn">
                            <?php if (isset($withdrawnDocs['error'])): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($withdrawnDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (!empty($withdrawnDocs)): ?>
                                <?php foreach ($withdrawnDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-700 whitespace-normal wrap-break-word">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['division_name'] ?? 'Not Routed'); ?>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <?php $bgClass = getStatusBadgeClass($doc['status_name']); ?>
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?php echo $bgClass; ?>">
                                                <?php echo htmlspecialchars($doc['status_name']); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="viewDocument(<?php echo $doc['document_id']; ?>)" class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50" title="View Document">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
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

    // Para sa action button kapag viniew yung document
    function viewDocument(documentId) {
        console.log("Viewing document ID:", documentId);
        // Dito natin idadagdag yung AJAX logic para sa View Modal
        // openModal('view-document-modal'); 
    }

    // Initialize pagination when page loads
    document.addEventListener('DOMContentLoaded', function () {
        window.paginationManagers = {};

        const tabs = [
            { tableBodyId: 'tableBody-referred',   paginationContainerId: 'paginationContainer-referred',   infoDisplayId: 'paginationInfo-referred' },
            { tableBodyId: 'tableBody-completed', paginationContainerId: 'paginationContainer-completed', infoDisplayId: 'paginationInfo-completed' },
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

</body>
</html>