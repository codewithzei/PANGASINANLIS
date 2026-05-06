<!-- Edit Role Modal Wrapper -->
<div id="routing-option-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('routing-option-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Routing Option</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('routing-option-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editRoutingOptionMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editRoutingOptionMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editRoutingOptionForm" class="p-6" method="POST" action="../../includes/actions_admin/edit_routing_option.php">
            <input type="hidden" id="routingOptionIdInput" name="routing_option_id">
            
            <div class="mb-4">
                <label for="editRoutingOptionInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Routing Option Name
                </label>
                <input 
                    type="text" 
                    id="editRoutingOptionInput"
                    name="routing_option_name"
                    placeholder="Enter routing option name"
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
                onclick="closeModal('routing-option-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editRoutingOptionForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit role - loads data into modal
function editRoutingOption(routingOptionId) {
    // Fetch role data via AJAX
    fetch('../../includes/fetch_routing_options_data.php?action=getRoutingOption&id=' + routingOptionId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('routingOptionIdInput').value = data.data.routing_option_id;
                document.getElementById('editRoutingOptionInput').value = data.data.routing_option_name;
                
                // Map status text to dropdown value
                const statusValue = data.data.status && data.data.status.toLowerCase() === 'active' ? '1' : '2';
                document.getElementById('editStatusSelect').value = statusValue;
                
                // Clear any previous messages
                const editRoutingOptionMessage = document.getElementById('editRoutingOptionMessage');
                if (editRoutingOptionMessage) {
                    editRoutingOptionMessage.classList.add('hidden');
                }
                
                // Open the modal
                openModal('routing-option-edit-modal');
            } else {
                alert('Error loading routing option data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching routing option data:', error);
            alert('Error loading routing option data. Please try again.');
        });
}

// Function to delete role
function deleteRoutingOption(routingOptionId, routingOptionName) {
    if (confirm(`Are you sure you want to delete the routing option "${routingOptionName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../includes/actions_admin/delete_routing_option.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'routing_option_id';
        input.value = routingOptionId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form and message when modal is opened via regular method
document.addEventListener('DOMContentLoaded', function() {
    const editRoutingOptionForm = document.getElementById('editRoutingOptionForm');
    
    if (editRoutingOptionForm) {
        // Optional: Add form submission validation
        editRoutingOptionForm.addEventListener('submit', function(e) {
            const routingOptionName = document.getElementById('editRoutingOptionInput').value.trim();
            const statusId = document.getElementById('editStatusSelect').value;
            
            if (!routingOptionName) {
                e.preventDefault();
                alert('Please enter a routing option name.');
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
