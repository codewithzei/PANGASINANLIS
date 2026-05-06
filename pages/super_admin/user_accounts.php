<?php

session_start();
include '../../components/header.php';

include '../../includes/fetch_users_data.php';

$users = getAllUsersData();
?>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'User Accounts';
        include '../../components/topbar.php';
        ?>

        <main class="p-6">
            <!-- Global Message Display -->
            <?php if (isset($_SESSION['user_status'])): ?>
                <div id="globalMessage"
                    class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 w-max max-w-md animate-fade-in shadow-lg rounded-lg"
                    style="<?php echo $_SESSION['user_status'] === 'success' ? 'background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);' : 'background-color: rgb(254, 226, 226); color: rgb(127, 29, 29); border: 1px solid rgb(252, 165, 165);'; ?>">
                    <div class="flex items-center gap-3 px-6 py-4">
                        <?php if ($_SESSION['user_status'] === 'success'): ?>
                            <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <?php
                        else: ?>
                            <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <?php
                        endif; ?>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['user_message']); ?></span>
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
                unset($_SESSION['user_status']);
                unset($_SESSION['user_message']);
                ?>
                <?php
            endif; ?>

           <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <!-- Header with Title -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">User Management</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage all system users</p>
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

                            <button onclick="openModal('user-add-modal')"
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
                                        Name
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
                                        Email
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
                                    onclick="sortTable(3)">
                                    <div class="flex items-center gap-2">
                                        Contact Number
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
                                    onclick="sortTable(4)">
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
                                    onclick="sortTable(5)">
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
                            <?php if (isset($users['error'])): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-red-600">
                                        <?php echo htmlspecialchars($users['error']); ?>
                                    </td>
                                </tr>
                                <?php
                            elseif (empty($users)): ?>
                                <!-- Empty state will be added by JavaScript -->
                                <?php
                            else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($user['user_account_id']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($user['profile_picture'])): ?>
                                                    <img src="../../<?php echo htmlspecialchars($user['profile_picture']); ?>"
                                                        alt="Profile"
                                                        class="h-10 w-10 rounded-full object-cover border border-gray-200 shrink-0"
                                                        onerror="this.onerror=null; this.src='../../assets/Province_of_Pangasinan.png';">
                                                    <?php
                                                else: ?>
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200 shrink-0 text-sm">
                                                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                                    </div>
                                                    <?php
                                                endif; ?>

                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php
                                                        // Format full name
                                                        $fullName = $user['first_name'] . ' ';
                                                        if (!empty($user['middle_name'])) {
                                                            $fullName .= substr($user['middle_name'], 0, 1) . '. ';
                                                        }
                                                        $fullName .= $user['last_name'];
                                                        if (!empty($user['suffix'])) {
                                                            $fullName .= ' ' . $user['suffix'];
                                                        }
                                                        echo htmlspecialchars($fullName);
                                                        ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        <?php echo htmlspecialchars($user['username']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($user['contact_number'] ?: 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($user['user_role_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $statusClass = $user['account_status'] === 'Active'
                                                ? 'bg-green-100 text-green-800'
                                                : ($user['account_status'] === 'Blocked' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-800');
                                            ?>
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                                <?php echo htmlspecialchars($user['account_status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-2">
                                                <button onclick="editUser(<?php echo $user['user_account_id']; ?>)"
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach; ?>
                                <?php
                            endif; ?>
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

<?php include 'modals/add_user.php'; ?>
<?php include 'modals/edit_user.php'; ?>
<script src="/PangasinanLIS/src/js/global.js"></script>
<script>
    function editUser(id) {
        fetch('../../includes/actions_super_admin/get_user.php?id=' + id)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    const user = res.data;
                    document.getElementById('edit_user_id').value = user.user_account_id;
                    document.getElementById('edit_first_name').value = user.first_name || '';
                    document.getElementById('edit_last_name').value = user.last_name || '';
                    document.getElementById('edit_middle_name').value = user.middle_name || '';
                    document.getElementById('edit_suffix').value = user.suffix || '';
                    document.getElementById('edit_contact_number').value = user.contact_number || '';
                    document.getElementById('edit_username').value = user.username || '';
                    document.getElementById('edit_email').value = user.email || '';
                    document.getElementById('edit_role_id').value = user.user_role_id || '';
                    document.getElementById('edit_account_status').value = user.account_status || '';

                    const preview = document.getElementById('editProfilePicturePreview');
                    window.currentEditProfilePictureUrl = user.profile_picture || null;
                    if (user.profile_picture) {
                        preview.innerHTML = `<img src="../../${user.profile_picture}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='../../assets/Province_of_Pangasinan.png';">`;
                    } else {
                        // Initials or Fallback SVG
                        const initials = ((user.first_name || '').charAt(0) + (user.last_name || '').charAt(0)).toUpperCase();
                        preview.innerHTML = `<div class="w-full h-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">${initials}</div>`;
                    }

                    openModal('user-edit-modal');
                } else {
                    alert('Failed to fetch user details: ' + (res.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error fetching user:', err);
                alert('An error occurred while fetching user data.');
            });
    }

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
                title: "No user found",
                subtitle: "Add your first user to get started"
            });
        }
    });
</script>

</body>

</html>