<!-- Edit Role Modal Wrapper -->
<div id="muni-city-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('muni-city-edit-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Municipality/City</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('muni-city-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editMuniCityMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editMuniCityMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editMuniCityForm" class="p-6" method="POST" action="../../includes/actions_super_admin/edit_muni_city.php">
            <input type="hidden" id="muniCityIdInput" name="muni_city_id">
            
            <div class="mb-4">
                <label for="editMuniCityInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Municipality/City Name
                </label>
                <input 
                    type="text" 
                    id="editMuniCityInput"
                    name="muni_city_name"
                    placeholder="Enter municipality/city name"
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
                onclick="closeModal('muni-city-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editMuniCityForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Function to edit role - loads data into modal
function editMuniCity(muniCityId) {
    // Fetch role data via AJAX
    fetch('../../includes/fetch_muni_cities_data.php?action=getMuniCity&id=' + muniCityId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Populate form fields
                document.getElementById('muniCityIdInput').value = data.data.muni_city_id;
                document.getElementById('editMuniCityInput').value = data.data.muni_city_name;
                
                // Map status text to dropdown value
                const statusValue = data.data.status && data.data.status.toLowerCase() === 'active' ? '1' : '2';
                document.getElementById('editStatusSelect').value = statusValue;
                
                // Clear any previous messages
                const editMuniCityMessage = document.getElementById('editMuniCityMessage');
                if (editMuniCityMessage) {
                    editMuniCityMessage.classList.add('hidden');
                }
                
                // Open the modal
                openModal('muni-city-edit-modal');
            } else {
                alert('Error loading municipality/city data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching municipality/city data:', error);
            alert('Error loading municipality/city data. Please try again.');
        });
}

// Function to delete role
function deleteMuniCity(muniCityId, muniCityName) {
    if (confirm(`Are you sure you want to delete the municipality/city "${muniCityName}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../includes/actions_super_admin/delete_muni_city.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'muni_city_id';
        input.value = muniCityId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form and message when modal is opened via regular method
document.addEventListener('DOMContentLoaded', function() {
    const editMuniCityForm = document.getElementById('editMuniCityForm');
    
    if (editMuniCityForm) {
        // Optional: Add form submission validation
        editMuniCityForm.addEventListener('submit', function(e) {
            const muniCityName = document.getElementById('editMuniCityInput').value.trim();
            const statusId = document.getElementById('editStatusSelect').value;
            
            if (!muniCityName) {
                e.preventDefault();
                alert('Please enter a municipality/city name.');
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
