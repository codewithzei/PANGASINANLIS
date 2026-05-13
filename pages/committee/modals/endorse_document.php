<?php 
$stmt = $pdo->prepare("SELECT opinion_office_id, opinion_office_code, opinion_office_name FROM opinion_offices WHERE status = 'active' ORDER BY opinion_office_name");
$stmt->execute();
$opinionOffices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Endorse to Offices Modal Wrapper -->
<div id="endorse-office-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="endorseModalOverlay" 
        onclick="closeModal('endorse-office-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="endorseModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-lg bg-white rounded-lg shadow-xl transition-all duration-300">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Endorse to Offices</h2>
            <button 
                id="endorseCloseBtn"
                onclick="closeModal('endorse-office-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="endorseMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium">
            <div id="endorseMessageContent"></div>
        </div>

        <!-- Modal Body -->
        <form id="endorseForm" class="p-6" method="POST" action="../../includes/actions_committee/process_endorse_document.php" onsubmit="event.preventDefault();">
            
            <!-- Hidden input for Document ID -->
            <input type="hidden" id="endorseDocumentIdInput" name="document_id">

            <!-- DOCUMENT INFO DISPLAY -->
            <div class="mb-5">
                <p class="text-xs text-gray-500 font-medium mb-1">Document</p>
                <p id="endorseDocumentTitleDisplay" class="text-sm font-semibold text-gray-800 leading-snug">
                    Loading document details...
                </p>
            </div>

            <!-- OFFICES CHECKBOXES (Using your reference style) -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Offices for Opinion
                </label>
                
                <!-- Box container with specific height and overflow -->
                <div class="border border-gray-200 rounded-lg p-3 max-h-56 overflow-y-auto bg-white custom-scrollbar">
                    <?php if(!empty($opinionOffices)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach($opinionOffices as $office): ?>
                                <!-- Reference Style Label/Box -->
                                <label class="flex items-center gap-2 p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-[#0033A1] cursor-pointer transition-colors duration-150">
                                    <input 
                                        type="checkbox" 
                                        name="office_ids[]" 
                                        value="<?php echo htmlspecialchars($office['opinion_office_id']); ?>" 
                                        class="w-4 h-4 rounded border-gray-600 text-[#0033A1] focus:ring-[#0033A1] cursor-pointer">
                                    <span class="text-sm text-gray-700 font-medium">
                                        <?php echo htmlspecialchars($office['opinion_office_code']); ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <p class="text-sm text-gray-400 text-center py-4">No offices available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- NOTES / INSTRUCTIONS -->
            <div class="mb-2">
                <label for="endorseInstructions" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes / Instructions
                </label>
                <textarea 
                    id="endorseInstructions"
                    name="instructions"
                    rows="3"
                    placeholder="Enter instructions for the requested opinion..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 resize-none"
                ></textarea>
            </div>

        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-4 p-5 border-t border-gray-200 bg-slate-50 rounded-b-lg">
            <button 
                type="button"
                onclick="closeModal('endorse-office-modal')"
                class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium transition duration-200 focus:outline-none cursor-pointer">
                Cancel
            </button>
            <button 
                type="button"
                id="confirmEndorseBtn"
                onclick="submitEndorseForm()"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/>
                    <path d="m21.854 2.147-10.94 10.939"/>
                </svg>
                Endorse Document
            </button>
        </div>
    </div>
</div>

<script>
// Opens the modal and populates document details
function openEndorseModal(documentId, trackingNo, subjectMatter) {
    // Reset the form and button state before opening
    const form = document.getElementById('endorseForm');
    if (form) form.reset();

    const btn = document.getElementById('confirmEndorseBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/>
                <path d="m21.854 2.147-10.94 10.939"/>
            </svg>
            Endorse Document`;
    }

    document.getElementById('endorseDocumentIdInput').value = documentId;
    document.getElementById('endorseDocumentTitleDisplay').textContent = trackingNo + ' - ' + subjectMatter;
    openModal('endorse-office-modal');
}

/**
 * Submits the endorse form via AJAX.
 * Prevents the browser from navigating to the PHP action file (which would show raw JSON).
 */
function submitEndorseForm() {
    const form = document.getElementById('endorseForm');
    if (!form) return;

    const btn = document.getElementById('confirmEndorseBtn');

    // Show loading state
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Processing...`;
    }

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        closeModal('endorse-office-modal');
        if (data.status === 'success') {
            showEndorseToast(data.message || 'Document endorsed successfully.', 'success');
            // Reload page after a short delay so the table reflects the updated status
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showEndorseToast(data.message || 'Please fix the errors and try again.', 'error');
            // Re-enable button on error
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/>
                        <path d="m21.854 2.147-10.94 10.939"/>
                    </svg>
                    Endorse Document`;
            }
        }
    })
    .catch((err) => {
        console.error(err);
        closeModal('endorse-office-modal');
        showEndorseToast('Network error. Please try again.', 'error');
    });
}

function showEndorseToast(message, type) {
    const existing = document.getElementById('endorseToast');
    if (existing) existing.remove();

    const isSuccess = type === 'success';
    const toast = document.createElement('div');
    toast.id = 'endorseToast';
    toast.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[999] flex items-center gap-3 px-6 py-4 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 opacity-0';
    toast.style.cssText = isSuccess
        ? 'background-color: rgb(220,252,231); color: rgb(20,83,45); border: 1px solid rgb(167,243,208);'
        : 'background-color: rgb(254,226,226); color: rgb(127,29,29); border: 1px solid rgb(252,165,165);';

    toast.innerHTML = isSuccess
        ? `<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span>${message}</span>`
        : `<svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg><span>${message}</span>`;

    document.body.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
    });

    // Auto-dismiss after 4s
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>