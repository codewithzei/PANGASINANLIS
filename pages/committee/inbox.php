<?php
session_start();
include '../../components/header.php';
include '../../includes/fetch_committee_inbox_data.php'; // Include the fetch file

$inboxDocs = getCommitteeInbox();
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">
        <?php
        $pageTitle = 'Inbox';
        include '../../components/topbar.php';
        ?>

        <main class="p-3 sm:p-6">
            <!-- Flash messages are handled by the JS showToast() in the receive_document modal -->

            <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <!-- Header with Title -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Inbox</h2>
                            <p class="text-sm text-gray-600 mt-1">Keep track of received documents awaiting confirmation</p>
                        </div>

                        <div class="flex items-center gap-3 w-full">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Search..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    onkeyup="filterTable()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="w-full overflow-x-auto">
                    <!-- Tinanggal ang table-fixed at w-full percentages -->
                    <table id="usersTable" class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <!-- Pinalitan ng px-6 py-3 at nilagyan ng whitespace-nowrap -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(0, 'usersTable', 'tableBody')">
                                    <div class="flex items-center gap-2">Tracking No.
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(1, 'usersTable', 'tableBody')">
                                    <div class="flex items-center gap-2">Subject Matter
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(2, 'usersTable', 'tableBody')">
                                    <div class="flex items-center gap-2">Document Type
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap cursor-pointer" onclick="sortTable(3, 'usersTable', 'tableBody')">
                                    <div class="flex items-center gap-2">Date Received
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody">
                            <?php if (isset($inboxDocs['error'])): ?>
                                <tr>
                                    <!-- Ginawang colspan="5" para sakop lahat ng columns -->
                                    <td colspan="5" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($inboxDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (empty($inboxDocs)): ?>
                                <!-- Empty state will be added by JavaScript -->
                            <?php else: ?>
                                <?php foreach ($inboxDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150" data-doc-id="<?php echo $doc['document_id']; ?>">
                                        <!-- LAHAT ay naging px-6 py-4 para pantay sa Header -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        
                                        <!-- Pwedeng mag-break ang Subject Matter pero may min-width na 250px para sa mobile -->
                                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-normal break-words min-w-[250px]">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($doc['created_at'])); ?>
                                            <br>
                                            <span class="text-xs text-gray-400"><?php echo date('h:i A', strtotime($doc['created_at'])); ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <button onclick="viewInboxDocument(<?php echo $doc['document_id']; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded transition duration-150 cursor-pointer" title="View Document">
                                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo" class="text-sm text-gray-700">
                        Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span
                            class="font-medium">0</span> results
                    </div>
                    <div id="paginationContainer" class="flex items-center space-x-2">
                        <!-- Pagination buttons will be generated by JavaScript -->
                    </div>
                </div>
            </div>
        </main>

    </div>
</div>

<?php include 'modals/receive_document.php'; ?>
<?php include 'modals/view_document.php'; ?>
<script src="/PangasinanLIS/src/js/global.js"></script>
<script src="/PangasinanLIS/src/js/page_transition.js"></script>
<script>
    // Initialize pagination when page loads
    document.addEventListener('DOMContentLoaded', function () {
        paginationManager = new PaginationManager({
            itemsPerPage: 10,
            tableBodyId: 'tableBody',
            paginationContainerId: 'paginationContainer',
            infoDisplayId: 'paginationInfo'
        });
        paginationManager.init();

        // Show empty state if no data initially
        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.querySelectorAll('tr:not(.empty-state)');
        if (rows.length === 0) {
            toggleEmptyState('tableBody', true, {
                title: "No data found",
                subtitle: "Add your first data to get started"
            });
        }
    });


</script>

</body>

</html>