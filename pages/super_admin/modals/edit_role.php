<!-- Edit Role Modal Wrapper -->
<div id="role-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('role-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Role</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('role-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editRoleMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editRoleMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editRoleForm" class="p-6" method="POST" action="../../includes/actions_super_admin/edit_role.php">
            <input type="hidden" id="roleIdInput" name="role_id">
            
            <div class="mb-4">
                <label for="editRoleInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Role Name
                </label>
                <input 
                    type="text" 
                    id="editRoleInput"
                    name="role_name"
                    placeholder="Enter role name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="editStatusSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select 
                    id="editStatusSelect"
                    name="data_status_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 bg-white"
                    required>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">
            <button 
                type="button"
                id="editCancelBtn"
                onclick="closeModal('role-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editRoleForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit role - loads data into modal
function editRole(roleId) {
    // Fetch role data via AJAX
    fetch('../../includes/fetch_roles_data.php?action=getRole&id=' + roleId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('roleIdInput').value = data.data.user_role_id;
                document.getElementById('editRoleInput').value = data.data.user_role_name;
                
                // Map status text to dropdown value
                const statusValue = data.data.status && data.data.status.toLowerCase() === 'active' ? '1' : '2';
                document.getElementById('editStatusSelect').value = statusValue;
                
                // Clear any previous messages
                const editRoleMessage = document.getElementById('editRoleMessage');
                if (editRoleMessage) {
                    editRoleMessage.classList.add('hidden');
                }
                
                // Open the modal
                openModal('role-edit-modal');
            } else {
                alert('Error loading role data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching role data:', error);
            alert('Error loading role data. Please try again.');
        });
}

// Function to delete role
function deleteRole(roleId, roleName) {
    if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../includes/actions_super_admin/delete_role.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'role_id';
        input.value = roleId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form and message when modal is opened via regular method
document.addEventListener('DOMContentLoaded', function() {
    const editRoleForm = document.getElementById('editRoleForm');
    
    if (editRoleForm) {
        // Optional: Add form submission validation
        editRoleForm.addEventListener('submit', function(e) {
            const roleName = document.getElementById('editRoleInput').value.trim();
            const statusId = document.getElementById('editStatusSelect').value;
            
            if (!roleName) {
                e.preventDefault();
                alert('Please enter a role name.');
                return false;
            }
            
            if (!statusId) {
                e.preventDefault();
                alert('Please select a status.');
                return false;
            }
        });
    }
});
</script>
