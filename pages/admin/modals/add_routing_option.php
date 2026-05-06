<!-- Add Role Modal Wrapper -->
<div id="routing-option-add-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="modalOverlay" 
        onclick="closeModal('routing-option-add-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="modal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Add Routing Option</h2>
            <button 
                id="closeBtn"
                onclick="closeModal('routing-option-add-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="routingOptionMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="routingOptionMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="routingOptionForm" class="p-6" method="POST" action="../../includes/actions_admin/add_routing_option.php">
            <div class="mb-4">
                <label for="routingOptionInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Routing Option Name
                </label>
                <input 
                    type="text" 
                    id="routingOptionInput"
                    name="routing_option_name"
                    placeholder="Enter routing option name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">
            <button 
                type="button"
                id="cancelBtn"
                onclick="closeModal('routing-option-add-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="routingOptionForm"
                id="addBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Add
            </button>
        </div>
    </div>
</div>

<script>
// Reset form when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addBtn');
    const modalId = 'routing-option-add-modal';
    
    // Override openModal function to reset form
    const originalOpenModal = window.openModal;
    window.openModal = function(id) {
        if (id === modalId) {
            // Reset the form
            const routingOptionForm = document.getElementById('routingOptionForm');
            if (routingOptionForm) {
                routingOptionForm.reset();
            }
            
            // Hide message if any
            const routingOptionMessage = document.getElementById('routingOptionMessage');
            if (routingOptionMessage) {
                routingOptionMessage.classList.add('hidden');
            }
        }
        // Call original function
        if (originalOpenModal) {
            originalOpenModal.call(this, id);
        }
    };
});
</script>
