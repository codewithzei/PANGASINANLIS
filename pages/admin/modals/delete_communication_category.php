<!-- Delete Role Modal Wrapper -->
<div id="communication-category-delete-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="deleteModalOverlay" 
        onclick="closeModal('communication-category-delete-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="deleteModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Delete Communication Category</h2>
            <button 
                id="deleteCloseBtn"
                onclick="closeModal('communication-category-delete-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Warning Icon -->
            <div class="flex justify-center mb-4">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
                Delete Communication Category?
            </h3>
            <p class="text-sm text-gray-600 text-center mb-6">
                Are you sure you want to delete the communication category <span id="deleteCommunicationCategoryName" class="font-semibold text-gray-800"></span>? This action will mark the communication category as deleted.
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200">
            <button 
                type="button"
                id="deleteCancelBtn"
                onclick="closeModal('communication-category-delete-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <form id="deleteCommunicationCategoryForm" method="POST" action="../../includes/actions_admin/delete_communication_category.php" class="inline">
                <input type="hidden" id="deleteCommunicationCategoryId" name="communication_category_id">
                <button 
                    type="submit"
                    id="confirmDeleteBtn"
                    class="px-5 py-2.5 text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Function to open delete role modal
function deleteCommunicationCategory(communicationCategoryId, communicationCategoryName) {
    document.getElementById('deleteCommunicationCategoryId').value = communicationCategoryId;
    document.getElementById('deleteCommunicationCategoryName').textContent = communicationCategoryName;
    openModal('communication-category-delete-modal');
}
</script>
