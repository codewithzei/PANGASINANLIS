<!-- Edit Opinion Status Modal Wrapper -->
<div id="opinion-status-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editOpinionStatusOverlay" 
        onclick="closeModal('opinion-status-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editOpinionStatusModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Opinion Status</h2>
            <button 
                id="editOpinionStatusCloseBtn"
                onclick="closeModal('opinion-status-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editOpinionStatusMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editOpinionStatusMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editOpinionStatusForm" class="p-6" method="POST" action="../../includes/actions_committee/edit_opinion_status.php">
            <input type="hidden" id="editOpinionStatusIdInput" name="opinion_status_id">

            <div class="mb-4">
                <label for="editOpinionStatusNameInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Opinion Status Name
                </label>
                <input 
                    type="text" 
                    id="editOpinionStatusNameInput"
                    name="opinion_status_name"
                    placeholder="Enter opinion status name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="editOpinionStatusSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select 
                    id="editOpinionStatusSelect"
                    name="status"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 bg-white"
                    required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">
            <button 
                type="button"
                id="editOpinionStatusCancelBtn"
                onclick="closeModal('opinion-status-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editOpinionStatusForm"
                id="updateOpinionStatusBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit opinion status - loads data into modal
function editOpinionStatus(statusId) {
    // Fetch data via AJAX
    fetch('../../includes/fetch_opinion_statuses_data.php?action=getOpinionStatus&id=' + statusId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('editOpinionStatusIdInput').value = data.data.opinion_status_id;
                document.getElementById('editOpinionStatusNameInput').value = data.data.opinion_status_name;

                // Set dropdown value ('active' or 'inactive')
                const statusValue = data.data.status ? data.data.status.toLowerCase() : 'active';
                document.getElementById('editOpinionStatusSelect').value = statusValue;

                // Clear any previous messages
                const editMessage = document.getElementById('editOpinionStatusMessage');
                if (editMessage) {
                    editMessage.classList.add('hidden');
                }

                // Open the modal
                openModal('opinion-status-edit-modal');
            } else {
                alert('Error loading data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Error loading data. Please try again.');
        });
}

// Client-side validation
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editOpinionStatusForm');

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const name = document.getElementById('editOpinionStatusNameInput').value.trim();
            const status = document.getElementById('editOpinionStatusSelect').value;

            if (!name) {
                e.preventDefault();
                alert('Please enter an opinion status name.');
                return false;
            }

            if (!status) {
                e.preventDefault();
                alert('Please select a status.');
                return false;
            }
        });
    }
});
</script>
