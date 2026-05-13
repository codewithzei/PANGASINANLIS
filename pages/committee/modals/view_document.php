<!-- VIEW DOCUMENT MODAL -->
<div id="view-document-modal" class="hidden">

    <!-- Overlay -->
    <div
        onclick="closeModal('view-document-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal Panel -->
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-102 w-[calc(100%-2rem)] md:w-full max-w-2xl bg-white rounded-xl shadow-2xl transition-all duration-300 max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Document Details</h2>
            <button
                type="button"
                onclick="closeModal('view-document-modal')"
                class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body (scrollable) -->
        <div class="overflow-y-auto flex-1 p-6">

            <!-- Loading State (shown on open, hidden after data loads) -->
            <div id="view-modal-loading" class="flex flex-col items-center justify-center py-16 gap-3">
                <svg class="animate-spin h-8 w-8 text-[#0033A1]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-slate-500 font-medium">Fetching details...</p>
            </div>

            <!-- Content (hidden until data loads) -->
            <div id="view-modal-content" class="hidden">

                <!-- Tracking Number Banner -->
                <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-100 flex justify-between items-center mb-5">
                    <span class="text-sm font-semibold text-blue-600 uppercase">Tracking Number</span>
                    <span id="view-tracking-no" class="text-lg font-bold text-[#0033A1]">—</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    <!-- Subject Matter (full width) -->
                    <div class="md:col-span-2">
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Subject Matter</span>
                        <p id="view-subject" class="text-slate-800 font-medium whitespace-normal bg-slate-50 p-3 rounded border border-slate-200 leading-relaxed">—</p>
                    </div>

                    <!-- Document Type -->
                    <div>
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Document Type</span>
                        <p id="view-doc-type" class="text-slate-800 font-medium">—</p>
                    </div>

                    <!-- Source / Origin -->
                    <div>
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Source / Origin</span>
                        <p id="view-source" class="text-slate-800 font-medium">—</p>
                    </div>

                    <!-- Current Division -->
                    <div>
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Current Division</span>
                        <p id="view-division" class="text-slate-800 font-medium">—</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Status</span>
                        <span id="view-status" class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-800">—</span>
                    </div>

                    <!-- Date Created in System -->
                    <div>
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Date Created in System</span>
                        <p id="view-date" class="text-slate-800 font-medium">—</p>
                    </div>

                    <!-- Remarks (full width) -->
                    <div class="md:col-span-2 pt-2 border-t border-slate-100">
                        <span class="block text-sm font-semibold text-slate-500 uppercase mb-1">Remarks / Notes</span>
                        <p id="view-remarks" class="text-sm text-slate-500 italic">No remarks provided.</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button
                type="button"
                onclick="closeModal('view-document-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Close
            </button>
            <button
                type="button"
                id="view-receive-btn"
                onclick="triggerReceiveFromView()"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer flex items-center gap-2">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Receive Document
            </button>
        </div>
    </div>
</div>

<script>
    // Stores the currently viewed document for the Receive shortcut
    let _viewDocData = null;

    /**
     * Full implementation of viewInboxDocument() – replaces the stub in inbox.php.
     * Opens the modal, shows loading, fetches full doc details via AJAX, then populates the fields.
     */
    function viewInboxDocument(docId) {
        _viewDocData = null;

        // Show loading, hide content, hide Receive btn
        document.getElementById('view-modal-content').classList.add('hidden');
        document.getElementById('view-modal-loading').classList.remove('hidden');
        document.getElementById('view-receive-btn').classList.add('hidden');

        openModal('view-document-modal');

        fetch('/PangasinanLIS/includes/fetch_inbox_data?action=getInboxDocument&id=' + docId)
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Error: ' + (data.message || 'Unknown error'));
                    closeModal('view-document-modal');
                    return;
                }

                const doc = data.data;
                _viewDocData = doc;

                // ── Tracking Number ────────────────────────────────────────
                document.getElementById('view-tracking-no').textContent = doc.tracking_number || 'N/A';

                // ── Core fields ────────────────────────────────────────────
                document.getElementById('view-subject').textContent   = doc.subject_matter              || 'N/A';
                document.getElementById('view-doc-type').textContent  = doc.document_type_name          || 'N/A';
                document.getElementById('view-division').textContent  = doc.current_division            || 'N/A';

                // ── Source (composite) ─────────────────────────────────────
                let sourceDisplay = doc.source_type_name || 'N/A';
                if (doc.external_office_name)        sourceDisplay = doc.external_office_name;
                else if (doc.hospital_name)          sourceDisplay = (doc.source_type_name || '') + ' — ' + doc.hospital_name;
                else if (doc.muni_city_name)         sourceDisplay = (doc.source_type_name || '') + ' (' + doc.muni_city_name + ')';
                document.getElementById('view-source').textContent = sourceDisplay;

                // ── Status badge ───────────────────────────────────────────
                const statusEl   = document.getElementById('view-status');
                const statusName = doc.status_name || 'Pending';
                statusEl.textContent = statusName;

                const statusMap = {
                    'pending': 'bg-amber-100 text-amber-800 border-amber-200',
                    'approved': 'bg-green-100 text-green-800 border-green-200',
                    'withdrawn': 'bg-gray-200 text-gray-800 border-gray-300',
                    'deferred': 'bg-orange-100 text-orange-800 border-orange-200',
                    'noted': 'bg-slate-100 text-slate-700 border-slate-200',
                    'under processing': 'bg-sky-100 text-sky-800 border-sky-200',
                    'lay on the table': 'bg-slate-200 text-slate-800 border-slate-300',
                    'referred': 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'remanded': 'bg-red-100 text-red-800 border-red-200',
                    'returned to plenary': 'bg-violet-100 text-violet-800 border-violet-200',
                    'for committee report': 'bg-cyan-100 text-cyan-800 border-cyan-200',
                    'for opinion': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'for calendar': 'bg-pink-100 text-pink-800 border-pink-200',
                    'on going': 'bg-teal-100 text-teal-800 border-teal-200'
                };

                const badgeClass = statusMap[statusName.toLowerCase()] || 'bg-blue-100 text-blue-800 border-blue-200';
                statusEl.className = 'inline-block text-xs font-semibold px-2.5 py-1 rounded-full border ' + badgeClass;

                // ── Date ───────────────────────────────────────────────────
                const dateObj = new Date(doc.created_at);
                document.getElementById('view-date').textContent = dateObj.toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                // ── Remarks ────────────────────────────────────────────────
                const remarksEl = document.getElementById('view-remarks');
                if (doc.remarks && doc.remarks.trim() !== '') {
                    remarksEl.textContent = doc.remarks;
                    remarksEl.classList.remove('italic', 'text-slate-400');
                    remarksEl.classList.add('text-slate-700');
                } else {
                    remarksEl.textContent = 'No remarks provided.';
                    remarksEl.classList.add('italic', 'text-slate-400');
                    remarksEl.classList.remove('text-slate-700');
                }

                // ── Show Receive btn only if doc not yet claimed ───────────
                if (!doc.current_owner_user_id) {
                    document.getElementById('view-receive-btn').classList.remove('hidden');
                }

                // ── Reveal content ─────────────────────────────────────────
                document.getElementById('view-modal-loading').classList.add('hidden');
                document.getElementById('view-modal-content').classList.remove('hidden');
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Something went wrong while fetching the document details.');
                closeModal('view-document-modal');
            });
    }

    /**
     * Closes the view modal and opens the receive confirmation modal.
     */
    function triggerReceiveFromView() {
        if (!_viewDocData) return;
        const doc = _viewDocData;
        closeModal('view-document-modal');
        setTimeout(() => {
            openReceiveDocumentModal(doc.document_id, doc.tracking_number, doc.subject_matter);
        }, 150);
    }
</script>