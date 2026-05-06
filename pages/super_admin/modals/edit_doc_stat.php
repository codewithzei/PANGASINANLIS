<!-- Edit Role Modal Wrapper -->
<div id="doc-stat-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('doc-stat-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Document Status</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('doc-stat-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editDocStatMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editDocStatMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editDocStatForm" class="p-6" method="POST" action="../../includes/actions_super_admin/edit_doc_stat.php">
            <input type="hidden" id="docStatIdInput" name="document_status_id">
            
            <div class="mb-4">
                <label for="editDocStatInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Document Status Name
                </label>
                <input 
                    type="text" 
                    id="editDocStatInput"
                    name="document_status_name"
                    placeholder="Enter document status name"
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
                onclick="closeModal('doc-stat-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editDocStatForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit role - loads data into modal
function editDocStat(docStatId) {
    // Fetch role data via AJAX
    fetch('../../includes/fetch_doc_stats_data.php?action=getDocStat&id=' + docStatId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('docStatIdInput').value = data.data.document_status_id;
                document.getElementById('editDocStatInput').value = data.data.document_status_name;
                
                // Map status text to dropdown value
                const statusValue = data.data.status && data.data.status.toLowerCase() === 'active' ? '1' : '2';
                document.getElementById('editStatusSelect').value = statusValue;
                
                // Clear any previous messages
                const editDocStatMessage = document.getElementById('editDocStatMessage');
                if (editDocStatMessage) {
                    editDocStatMessage.classList.add('hidden');
                }
                
                // Open the modal
                openModal('doc-stat-edit-modal');
            } else {
                alert('Error loading document status data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching document status data:', error);
            alert('Error loading document status data. Please try again.');
        });
}

// Function to delete role
function deleteDocStat(docStatId, docStatName) {
    if (confirm(`Are you sure you want to delete the document status "${docStatName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../includes/actions_super_admin/delete_doc_stat.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'document_status_id';
        input.value = docStatId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form and message when modal is opened via regular method
document.addEventListener('DOMContentLoaded', function() {
    const editDocStatForm = document.getElementById('editDocStatForm');
    
    if (editDocStatForm) {
        // Optional: Add form submission validation
        editDocStatForm.addEventListener('submit', function(e) {
            const docStatName = document.getElementById('editDocStatInput').value.trim();
            const statusId = document.getElementById('editStatusSelect').value;
            
            if (!docStatName) {
                e.preventDefault();
                alert('Please enter a document status name.');
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
