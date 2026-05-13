<!-- Edit Opinion Office Modal Wrapper -->
<div id="opinion-office-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('opinion-office-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Opinion Office</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('opinion-office-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editOpinionOfficeMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editOpinionOfficeMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <!-- Pinalitan ko ng edit_opinion_office.php para mag-match doon sa file na ginawa natin kanina -->
        <form id="editOpinionOfficeForm" class="p-6" method="POST" action="../../includes/actions_committee/edit_opinion_office.php">
            <input type="hidden" id="opinionOfficeIdInput" name="opinion_office_id">

            <div class="mb-4">
                <label for="editOpinionOfficeCodeInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Opinion Office Code
                </label>
                <input 
                    type="text" 
                    id="editOpinionOfficeCodeInput"
                    name="opinion_office_code"
                    placeholder="Enter office code"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="editOpinionOfficeNameInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Opinion Office Name
                </label>
                <input 
                    type="text" 
                    id="editOpinionOfficeNameInput"
                    name="opinion_office_name"
                    placeholder="Enter office name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="editStatusSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select 
                    id="editStatusSelect"
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
                id="editCancelBtn"
                onclick="closeModal('opinion-office-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editOpinionOfficeForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit opinion office - loads data into modal
function editOpinionOffice(officeId) {
    // Fetch data via AJAX using the PHP file we created earlier
    fetch('../../includes/fetch_opinion_offices_data.php?action=getOpinionOffice&id=' + officeId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('opinionOfficeIdInput').value = data.data.opinion_office_id;
                document.getElementById('editOpinionOfficeCodeInput').value = data.data.opinion_office_code;
                document.getElementById('editOpinionOfficeNameInput').value = data.data.opinion_office_name;
                
                // Set dropdown value ('active' or 'inactive')
                const statusValue = data.data.status ? data.data.status.toLowerCase() : 'active';
                document.getElementById('editStatusSelect').value = statusValue;
                
                // Clear any previous messages
                const editMessage = document.getElementById('editOpinionOfficeMessage');
                if (editMessage) {
                    editMessage.classList.add('hidden');
                }
                
                // Open the modal
                openModal('opinion-office-edit-modal');
            } else {
                alert('Error loading data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Error loading data. Please try again.');
        });
}

// Function to delete opinion office
function deleteOpinionOffice(officeId, officeName) {
    if (confirm(`Are you sure you want to delete "${officeName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        // Tinuro sa process file natin kanina
        form.action = '../../includes/actions_committee/delete_opinion_office.php';
        
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'opinion_office_id';
        inputId.value = officeId;

        const inputName = document.createElement('input');
        inputName.type = 'hidden';
        inputName.name = 'opinion_office_name';
        inputName.value = officeName;
        
        form.appendChild(inputId);
        form.appendChild(inputName);
        document.body.appendChild(form);
        form.submit();
    }
}

// Optional: Client-side validation
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editOpinionOfficeForm');
    
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const code = document.getElementById('editOpinionOfficeCodeInput').value.trim();
            const name = document.getElementById('editOpinionOfficeNameInput').value.trim();
            const status = document.getElementById('editStatusSelect').value;
            
            if (!code || !name) {
                e.preventDefault();
                alert('Please enter both office code and name.');
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