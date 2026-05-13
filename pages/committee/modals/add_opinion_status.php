<!-- Add Opinion Status Modal Wrapper -->
<div id="opinion-status-add-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="opinionStatusOverlay" 
        onclick="closeModal('opinion-status-add-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="opinionStatusModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Add Opinion Status</h2>
            <button 
                id="opinionStatusCloseBtn"
                onclick="closeModal('opinion-status-add-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="opinionStatusMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="opinionStatusMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="opinionStatusForm" class="p-6" method="POST" action="../../includes/actions_committee/add_opinion_status.php">
            <div class="mb-4">
                <label for="opinionStatusInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Opinion Status Name
                </label>
                <input 
                    type="text" 
                    id="opinionStatusInput"
                    name="opinion_status_name"
                    placeholder="Enter opinion status name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">
            <button 
                type="button"
                id="opinionStatusCancelBtn"
                onclick="closeModal('opinion-status-add-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="opinionStatusForm"
                id="opinionStatusAddBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Add
            </button>
        </div>
    </div>
</div>

<script>
// Reset form when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    const modalId = 'opinion-status-add-modal';
    
    // Override openModal function to reset form
    const originalOpenModal = window.openModal;
    window.openModal = function(id) {
        if (id === modalId) {
            // Reset the form
            const opinionStatusForm = document.getElementById('opinionStatusForm');
            if (opinionStatusForm) {
                opinionStatusForm.reset();
            }
            
            // Hide message if any
            const opinionStatusMessage = document.getElementById('opinionStatusMessage');
            if (opinionStatusMessage) {
                opinionStatusMessage.classList.add('hidden');
            }
        }
        // Call original function
        if (originalOpenModal) {
            originalOpenModal.call(this, id);
        }
    };
});
</script>