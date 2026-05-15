<!-- Receive Document Confirmation Modal -->
<div id="receive-document-modal" class="hidden">

    <!-- Overlay -->
    <div
        id="receiveDocumentOverlay"
        onclick="closeModal('receive-document-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal Panel -->
    <div
        id="receiveDocumentPanel"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-102 w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-xl shadow-2xl transition-all duration-300">

        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Document Details</h2>
            <button
                type="button"
                onclick="closeModal('receive-document-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div class="flex items-center justify-center h-14 w-14 rounded-full bg-blue-100">
                    <svg class="h-7 w-7 text-[#0033A1]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-1">
                Confirm Receipt
            </h3>

            <!-- Description -->
            <p class="text-sm text-gray-500 text-center mb-5">
                Are you sure you want to receive the document:
            </p>

            <!-- Document Info Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 mb-2 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-0.5">Tracking No.</p>
                <p id="receiveDocTrackingNo" class="text-base font-bold text-[#0033A1]">—</p>
                <p id="receiveDocSubjectMatter" class="text-sm text-gray-600 mt-1">—</p>
            </div>

            <p class="text-xs text-gray-400 text-center">
                This will mark the document as received and assign it to your account.
            </p>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button
                type="button"
                onclick="closeModal('receive-document-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button
                type="button"
                id="confirmReceiveBtn"
                onclick="confirmReceiveDocument()"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer  flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
                Receive
            </button>
        </div>
    </div>
</div>

<script>
    // Holds the document ID to be received
    let _pendingReceiveDocId = null;

    /**
     * Opens the receive confirmation modal.
     * @param {number} documentId
     * @param {string} trackingNumber
     * @param {string} subjectMatter
     */
    function openReceiveDocumentModal(documentId, trackingNumber, subjectMatter) {
        _pendingReceiveDocId = documentId;

        document.getElementById('receiveDocTrackingNo').textContent = trackingNumber || '—';
        document.getElementById('receiveDocSubjectMatter').textContent = subjectMatter || '—';

        // Reset button state
        const btn = document.getElementById('confirmReceiveBtn');
        btn.disabled = false;
        btn.innerHTML = `
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5"/>
            </svg>
            Receive`;

        openModal('receive-document-modal');
    }

    /**
     * Sends the AJAX request to process the document receipt.
     */
    function confirmReceiveDocument() {
        if (!_pendingReceiveDocId) return;

        const btn = document.getElementById('confirmReceiveBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Processing...`;

        const formData = new FormData();
        formData.append('action', 'receiveDocument');
        formData.append('document_id', _pendingReceiveDocId);

        // BAGO: Nilagyan ng .php at siniguradong tama ang pangalan ng action file na ginawa natin kanina!
        // Kung iniba mo ang pangalan ng file kanina, palitan mo rin ito para mag-match.
        fetch('/PangasinanLIS/includes/actions_committee/process_receive_document.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            closeModal('receive-document-modal');
            if (data.status === 'success') {
                showToast(data.message || 'Document received successfully.', 'success');
                // Remove the row from the table after a short delay
                setTimeout(() => {
                    const row = document.querySelector(`tr[data-doc-id="${_pendingReceiveDocId}"]`);
                    if (row) {
                        row.remove();
                        if (typeof paginationManager !== 'undefined') {
                            paginationManager.init();
                        }
                        // Show empty state if no data rows remain
                        const remaining = document.querySelectorAll('#tableBody tr:not(.empty-state)');
                        if (remaining.length === 0 && typeof toggleEmptyState === 'function') {
                            toggleEmptyState('tableBody', true, {
                                title: 'No documents found',
                                subtitle: 'No documents awaiting confirmation'
                            });
                        }
                    } else {
                        // Fallback: reload the page
                        window.location.reload();
                    }
                    _pendingReceiveDocId = null;
                }, 800);
            } else {
                showToast(data.message || 'An error occurred. Please try again.', 'error');
                _pendingReceiveDocId = null;
            }
        })
        .catch((err) => {
            console.error(err); // Para makita mo sa console kung may iba pang error
            closeModal('receive-document-modal');
            showToast('Network error. Please try again.', 'error');
            _pendingReceiveDocId = null;
        });
    }

    /**
     * Shows a temporary toast notification.
     * @param {string} message
     * @param {'success'|'error'} type
     */
    function showToast(message, type) {
        // Remove existing toast if any
        const existing = document.getElementById('receiveToast');
        if (existing) existing.remove();

        const isSuccess = type === 'success';
        const toast = document.createElement('div');
        toast.id = 'receiveToast';
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