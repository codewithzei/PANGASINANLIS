<?php
session_start();
include '../../components/header.php';
// Siguraduhing tama ang path papunta sa backend file natin
include '../../includes/fetch_sp_received_documents_data.php'; 

// Kunin ang ID ng nakalogin na user
$userId = $_SESSION['user_id'] ?? null;

// Kung walang nakalogin, i-redirect sa login page (basic security)
if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

// Fetch the received documents para sa user na ito
$receivedDocs = getSpReceivedDocuments($userId);
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">
        <?php
        $pageTitle = 'Inbox';
        include '../../components/topbar.php';
        ?>

        <main class="p-3 sm:p-6">
            <!-- Global Message Display -->
            <?php 
    // 1. SALUHIN KUNG ANONG MESSAGE ANG MERON (Route man o Receive)
    $displayStatus = null;
    $displayMessage = null;

    if (isset($_SESSION['route_status'])) {
        $displayStatus = $_SESSION['route_status'];
        $displayMessage = $_SESSION['route_message'];
        unset($_SESSION['route_status'], $_SESSION['route_message']);
    } elseif (isset($_SESSION['receive_status'])) {
        $displayStatus = $_SESSION['receive_status'];
        $displayMessage = $_SESSION['receive_message'];
        unset($_SESSION['receive_status'], $_SESSION['receive_message']);
    }
    ?>

    <!-- 2. I-DISPLAY ANG TOAST KUNG MAY LAMAN YUNG VARIABLE -->
    <?php if ($displayStatus && $displayMessage): ?>
        <div id="globalMessage"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 w-max max-w-md animate-fade-in shadow-lg rounded-lg"
            style="<?php echo $displayStatus === 'success' ? 'background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);' : 'background-color: rgb(254, 226, 226); color: rgb(127, 29, 29); border: 1px solid rgb(252, 165, 165);'; ?>">
            
            <div class="flex items-center gap-3 px-6 py-4">
                <?php if ($displayStatus === 'success'): ?>
                    <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                <?php else: ?>
                    <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                <?php endif; ?>
                
                <span class="text-sm font-medium"><?php echo htmlspecialchars($displayMessage); ?></span>
                
                <button onclick="document.getElementById('globalMessage').remove()" class="text-current opacity-70 hover:opacity-100 transition ml-2 cursor-pointer">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <script>
            // Automatic hide after 4 seconds
            setTimeout(() => {
                const msg = document.getElementById('globalMessage');
                if(msg) {
                    msg.style.transition = 'opacity 0.3s ease';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 300);
                }
            }, 4000);
        </script>
    <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <!-- Header with Title -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Received Documents</h2>
                            <p class="text-sm text-gray-600 mt-1">Official records and materials submitted for review and processing</p>
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
                            <?php if (isset($receivedDocs['error'])): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($receivedDocs['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (empty($receivedDocs)): ?>
                                <!-- Empty state will be added by JavaScript -->
                            <?php else: ?>
                                <?php foreach ($receivedDocs as $doc): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <!-- LAHAT ay naging px-6 py-4 para pantay sa Header -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#0033A1]">
                                            <?php echo htmlspecialchars($doc['tracking_number']); ?>
                                        </td>
                                        
                                        <!-- Pwedeng mag-break ang Subject Matter pero may min-width na 250px -->
                                        <td class="px-6 py-4 text-sm text-gray-700 break-words min-w-[250px]">
                                            <?php echo htmlspecialchars($doc['subject_matter']); ?>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($doc['document_type_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($doc['updated_at'])); ?>
                                            <br>
                                            <span class="text-xs text-gray-400"><?php echo date('h:i A', strtotime($doc['updated_at'])); ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <a href="process_document.php?id=<?php echo $doc['document_id']; ?>" class="text-gray-500 hover:text-gray-800 p-1 rounded hover:bg-gray-100">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Add more rows as needed -->
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
<script src="/PangasinanLIS/src/js/global.js"></script>
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