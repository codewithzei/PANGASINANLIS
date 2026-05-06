<?php 
include '../../includes/db.php';

$stmt = $pdo->prepare("SELECT document_type_id AS id, document_type_name FROM document_types WHERE status = 'active' ORDER BY document_type_name");
$stmt->execute();
$documentTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Add Checklist Modal Wrapper -->
<div id="checklist-add-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="modalOverlay" 
        onclick="closeModal('checklist-add-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="modal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-101 w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Add Requirement</h2>
            <button 
                id="closeBtn"
                onclick="closeModal('checklist-add-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="checklistMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="checklistMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="checklistForm" class="p-6" method="POST" action="../../includes/actions_admin/add_requirement.php">
            
            <div class="mb-4">
                <label for="requirementInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Requirement Name
                </label>
                <input 
                    type="text" 
                    id="requirementInput"
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
                <div class="relative" id="addDocTypeWrapper">
                    <button 
                        type="button"
                        id="addDocTypeToggle"
                        onclick="toggleAddDocTypeDropdown()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 bg-white text-left flex items-center justify-between">
                        <span id="addDocTypePlaceholder" class="text-gray-400 text-sm">Select document types...</span>
                        <svg class="h-4 w-4 text-gray-400 transition-transform duration-200" id="addDocTypeChevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown list -->
                    <div id="addDocTypeDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <?php if (empty($documentTypes)): ?>
                            <div class="px-4 py-3 text-sm text-gray-500 text-center">No document types available</div>
                        <?php else: ?>
                            <?php foreach ($documentTypes as $docType): ?>
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors duration-150">
                                <input 
                                    type="checkbox" 
                                    name="doc_type_ids[]" 
                                    value="<?php echo $docType['id']; ?>"
                                    class="add-doc-type-checkbox w-4 h-4 rounded border-gray-300 text-[#0033A1] focus:ring-[#0033A1] cursor-pointer"
                                    onchange="updateAddDocTypeLabel()">
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($docType['document_type_name']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Selected tags display -->
                <div id="addDocTypeTags" class="flex flex-wrap gap-2 mt-2 hidden"></div>
            </div>

        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200">
            <button 
                type="button"
                id="cancelBtn"
                onclick="closeModal('checklist-add-modal')"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="checklistForm"
                id="addBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Add
            </button>
        </div>
    </div>
</div>

<script>
// Toggle add doc type dropdown
function toggleAddDocTypeDropdown() {
    const dropdown = document.getElementById('addDocTypeDropdown');
    const chevron = document.getElementById('addDocTypeChevron');
    dropdown.classList.toggle('hidden');
    chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('addDocTypeWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const dropdown = document.getElementById('addDocTypeDropdown');
        const chevron = document.getElementById('addDocTypeChevron');
        if (dropdown) dropdown.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
});

// Update the label and tags based on checked items
function updateAddDocTypeLabel() {
    const checkboxes = document.querySelectorAll('.add-doc-type-checkbox:checked');
    const placeholder = document.getElementById('addDocTypePlaceholder');
    const tagsContainer = document.getElementById('addDocTypeTags');

    // Update placeholder text
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

        // Render tags
        tagsContainer.innerHTML = '';
        tagsContainer.classList.remove('hidden');
        checkboxes.forEach(cb => {
            const label = cb.closest('label').querySelector('span').textContent;
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full';
            tag.innerHTML = `${label} <button type="button" onclick="removeAddDocTag(${cb.value})" class="ml-0.5 hover:text-blue-600 cursor-pointer">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
            tagsContainer.appendChild(tag);
        });
    }
}

function removeAddDocTag(docTypeId) {
    const checkbox = document.querySelector(`.add-doc-type-checkbox[value="${docTypeId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateAddDocTypeLabel();
    }
}

// Reset form when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    const modalId = 'checklist-add-modal';
    
    const originalOpenModal = window.openModal;
    window.openModal = function(id) {
        if (id === modalId) {
            const checklistForm = document.getElementById('checklistForm');
            if (checklistForm) checklistForm.reset();
            
            // Uncheck all doc type checkboxes
            document.querySelectorAll('.add-doc-type-checkbox').forEach(cb => cb.checked = false);
            updateAddDocTypeLabel();
            
            // Close dropdown
            const dropdown = document.getElementById('addDocTypeDropdown');
            const chevron = document.getElementById('addDocTypeChevron');
            if (dropdown) dropdown.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';

            // Hide message
            const checklistMessage = document.getElementById('checklistMessage');
            if (checklistMessage) checklistMessage.classList.add('hidden');
        }
        if (originalOpenModal) originalOpenModal.call(this, id);
    };
});
</script>
