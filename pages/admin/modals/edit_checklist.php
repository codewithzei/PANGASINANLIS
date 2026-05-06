<?php 
include '../../includes/db.php';

$stmt = $pdo->prepare("SELECT document_type_id AS id, document_type_name FROM document_types WHERE status = 'active' ORDER BY document_type_name");
$stmt->execute();
$documentTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Edit Checklist Modal Wrapper -->
<div id="checklist-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('checklist-edit-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-101 w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Requirement</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('checklist-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editChecklistMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="editChecklistMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="editChecklistForm" class="p-6" method="POST" action="../../includes/actions_admin/edit_requirement.php">
            <input type="hidden" id="requirementIdInput" name="requirement_id">

            <div class="mb-4">
                <label for="editRequirementInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Requirement Name
                </label>
                <input 
                    type="text" 
                    id="editRequirementInput"
                    name="requirement_name"
                    placeholder="Enter requirement name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Document Types <span class="text-gray-400 font-normal">(select all that apply)</span>
                </label>

                <!-- Custom multiselect dropdown -->
                <div class="relative" id="editDocTypeWrapper">
                    <button 
                        type="button"
                        id="editDocTypeToggle"
                        onclick="toggleEditDocTypeDropdown()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 bg-white text-left flex items-center justify-between">
                        <span id="editDocTypePlaceholder" class="text-gray-400 text-sm">Select document types...</span>
                        <svg class="h-4 w-4 text-gray-400 transition-transform duration-200" id="editDocTypeChevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown list -->
                    <div id="editDocTypeDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <?php if (empty($documentTypes)): ?>
                            <div class="px-4 py-3 text-sm text-gray-500 text-center">No document types available</div>
                        <?php else: ?>
                            <?php foreach ($documentTypes as $docType): ?>
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors duration-150">
                                <input 
                                    type="checkbox" 
                                    name="doc_type_ids[]" 
                                    value="<?php echo $docType['id']; ?>"
                                    class="edit-doc-type-checkbox w-4 h-4 rounded border-gray-300 text-[#0033A1] focus:ring-[#0033A1] cursor-pointer"
                                    onchange="updateEditDocTypeLabel()">
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($docType['document_type_name']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Selected tags display -->
                <div id="editDocTypeTags" class="flex flex-wrap gap-2 mt-2 hidden"></div>
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
                onclick="closeModal('checklist-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editChecklistForm"
                id="updateBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Update
            </button>
        </div>
    </div>
</div>

<script>
// Toggle edit doc type dropdown
function toggleEditDocTypeDropdown() {
    const dropdown = document.getElementById('editDocTypeDropdown');
    const chevron = document.getElementById('editDocTypeChevron');
    dropdown.classList.toggle('hidden');
    chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Close edit dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('editDocTypeWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const dropdown = document.getElementById('editDocTypeDropdown');
        const chevron = document.getElementById('editDocTypeChevron');
        if (dropdown) dropdown.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
});

// Update the label and tags based on checked items
function updateEditDocTypeLabel() {
    const checkboxes = document.querySelectorAll('.edit-doc-type-checkbox:checked');
    const placeholder = document.getElementById('editDocTypePlaceholder');
    const tagsContainer = document.getElementById('editDocTypeTags');

    if (checkboxes.length === 0) {
        placeholder.textContent = 'Select document types...';
        placeholder.classList.add('text-gray-400');
        placeholder.classList.remove('text-gray-800');
        tagsContainer.classList.add('hidden');
        tagsContainer.innerHTML = '';
    } else {
        placeholder.textContent = checkboxes.length === 1 ? '1 type selected' : `${checkboxes.length} types selected`;
        placeholder.classList.remove('text-gray-400');
        placeholder.classList.add('text-gray-800');

        tagsContainer.innerHTML = '';
        tagsContainer.classList.remove('hidden');
        checkboxes.forEach(cb => {
            const label = cb.closest('label').querySelector('span').textContent;
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full';
            tag.innerHTML = `${label} <button type="button" onclick="removeEditDocTag(${cb.value})" class="ml-0.5 hover:text-blue-600 cursor-pointer">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
            tagsContainer.appendChild(tag);
        });
    }
}

function removeEditDocTag(docTypeId) {
    const checkbox = document.querySelector(`.edit-doc-type-checkbox[value="${docTypeId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateEditDocTypeLabel();
    }
}

// Function to load requirement data into the edit modal
function editChecklist(requirementId) {
    fetch('../../includes/fetch_checklists_data.php?action=getRequirement&id=' + requirementId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('requirementIdInput').value = data.data.requirement_id;
                document.getElementById('editRequirementInput').value = data.data.requirement_name;

                // Set status
                document.getElementById('editStatusSelect').value = data.data.status || 'active';

                // Clear all doc type checkboxes first
                document.querySelectorAll('.edit-doc-type-checkbox').forEach(cb => cb.checked = false);

                // Check the ones linked to this requirement
                if (data.data.document_type_ids) {
                    const ids = data.data.document_type_ids.toString().split(',').map(id => id.trim());
                    ids.forEach(id => {
                        const cb = document.querySelector(`.edit-doc-type-checkbox[value="${id}"]`);
                        if (cb) cb.checked = true;
                    });
                }

                // Update label/tags
                updateEditDocTypeLabel();

                // Close dropdown if open
                const dropdown = document.getElementById('editDocTypeDropdown');
                const chevron = document.getElementById('editDocTypeChevron');
                if (dropdown) dropdown.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';

                // Clear messages
                const editChecklistMessage = document.getElementById('editChecklistMessage');
                if (editChecklistMessage) editChecklistMessage.classList.add('hidden');

                openModal('checklist-edit-modal');
            } else {
                alert('Error loading requirement data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching requirement data:', error);
            alert('Error loading requirement data. Please try again.');
        });
}

// Function to delete checklist (requirement)
function deleteChecklist(requirementId, requirementName) {
    document.getElementById('deleteChecklistId').value = requirementId;
    document.getElementById('deleteChecklistName').textContent = requirementName;
    openModal('checklist-delete-modal');
}

// Form validation on submit
document.addEventListener('DOMContentLoaded', function() {
    const editChecklistForm = document.getElementById('editChecklistForm');
    
    if (editChecklistForm) {
        editChecklistForm.addEventListener('submit', function(e) {
            const requirementName = document.getElementById('editRequirementInput').value.trim();
            
            if (!requirementName) {
                e.preventDefault();
                alert('Please enter a requirement name.');
                return false;
            }
        });
    }
});
</script>
