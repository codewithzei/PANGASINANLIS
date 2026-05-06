<?php
session_start();
include '../../components/header.php';
include '../../includes/fetch_roles_data.php'; // Include the fetch file

$roles = getAllRoles();
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'User Roles';
        include '../../components/topbar.php';
        ?>

        <main class="p-6">
            <!-- Global Message Display -->
            <?php if (isset($_SESSION['role_status'])): ?>
                <div id="globalMessage"
                    class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 w-max max-w-md animate-fade-in shadow-lg rounded-lg"
                    style="<?php echo $_SESSION['role_status'] === 'success' ? 'background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);' : 'background-color: rgb(254, 226, 226); color: rgb(127, 29, 29); border: 1px solid rgb(252, 165, 165);'; ?>">
                    <div class="flex items-center gap-3 px-6 py-4">
                        <?php if ($_SESSION['role_status'] === 'success'): ?>
                            <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        <?php endif; ?>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['role_message']); ?></span>
                        <button onclick="closeMessage()" class="text-current opacity-70 hover:opacity-100 transition ml-2">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php
                // Clear session data after displaying
                unset($_SESSION['role_status']);
                unset($_SESSION['role_message']);
                ?>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <!-- Header with Title -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">User Roles</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage and configure user roles and access</p>
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
                                <input type="text" id="searchInput" placeholder="Search roles..."
                                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                    onkeyup="filterTable()">
                            </div>

                            <button onclick="openModal('role-add-modal')"
                                class="px-4 py-2 bg-[#0033A1] text-white rounded-lg hover:bg-blue-800 transition-colors duration-200 font-medium flex items-center gap-2 cursor-pointer whitespace-nowrap">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto">
                    <table id="usersTable" class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer"
                                    onclick="sortTable(0)">
                                    <div class="flex items-center gap-2">
                                        ID
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="0"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer"
                                    onclick="sortTable(1)">
                                    <div class="flex items-center gap-2">
                                        Role
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="1"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer"
                                    onclick="sortTable(2)">
                                    <div class="flex items-center gap-2">
                                        Status
                                        <svg class="h-4 w-4 text-gray-400 sort-icon" data-column="4"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody">
                            <?php if (isset($roles['error'])): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($roles['error']); ?>
                                    </td>
                                </tr>
                            <?php elseif (empty($roles)): ?>
                                <!-- Empty state will be added by JavaScript -->
                            <?php else: ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($role['user_role_id']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($role['user_role_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $statusClass = $role['status'] === 'active'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-200 text-gray-800';
                                            ?>
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars(ucfirst($role['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <button onclick="editRole(<?php echo $role['user_role_id']; ?>)"
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    onclick="deleteRole(<?php echo $role['user_role_id']; ?>, '<?php echo htmlspecialchars($role['user_role_name']); ?>')"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
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
                    class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
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

<?php include 'modals/add_role.php'; ?>
<?php include 'modals/edit_role.php'; ?>
<?php include 'modals/delete_role.php'; ?>
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