<div id="resolve-opinion-modal" class="hidden">
    <div id="resolveOpinionModalOverlay" onclick="closeModal('resolve-opinion-modal')"
        class="fixed inset-0 bg-black/75 z-[102] transition-opacity duration-300 cursor-pointer"></div>

    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[102] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl flex flex-col">

        <div class="flex items-start justify-between gap-3 p-5 border-b border-gray-200">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Resolve mixed opinions</h2>
                <p class="mt-1 text-sm text-gray-600">One or more offices returned an unfavorable opinion while others are favorable. Choose how to continue.</p>
            </div>
            <button type="button" onclick="closeModal('resolve-opinion-modal')"
                class="shrink-0 text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6 flex flex-col gap-3">
            <input type="hidden" id="resolveDocumentIdInput" value="">
            <input type="hidden" id="resolveTrackingCache" value="">
            <input type="hidden" id="resolveSubjectCache" value="">
            <input type="hidden" id="resolvePendingResolution" value="">

            <!-- PROCEED BUTTON (Amber Theme) -->
            <button type="button" onclick="submitResolveProceed()"
                class="w-full py-3 px-4 rounded-lg font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 border border-amber-200 transition-colors cursor-pointer text-left flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Proceed to agenda<br><span class="text-xs font-normal text-amber-800">Mark unfavorable offices as skipped and move document to Ready for Agenda.</span></span>
            </button>

            <!-- WITHDRAW BUTTON (Gray Theme) -->
            <button type="button" onclick="submitResolveWithdraw()"
                class="w-full py-3 px-4 rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-gray-400 border border-gray-400 transition-colors cursor-pointer text-left flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Withdraw document<br><span class="text-xs font-normal text-gray-500">Set document status to Withdrawn.</span></span>
            </button>
        </div>
    </div>
</div>

<!-- Confirmation (stacked above resolve modal) -->
<div id="resolve-confirm-modal" class="hidden">
    <div onclick="closeResolveConfirmModal()"
        class="fixed inset-0 bg-black/75 z-[104] transition-opacity duration-300 cursor-pointer"></div>

    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[104] w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-lg shadow-xl flex flex-col">
        <div class="flex items-start justify-between gap-3 p-5 border-b border-gray-200">
            <h2 id="resolveConfirmTitle" class="text-xl font-bold text-gray-800">Confirm</h2>
            <button type="button" onclick="closeResolveConfirmModal()"
                class="shrink-0 text-gray-400 hover:text-gray-600 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <p id="resolveConfirmMessage" class="text-sm text-gray-600 leading-relaxed"></p>
            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-gray-800">
                <p class="font-semibold text-[#0033A1]"><span class="text-gray-500 font-medium">Tracking:</span> <span id="resolveConfirmTracking"></span></p>
                <p class="mt-1 text-gray-700"><span class="text-gray-500 font-medium">Subject:</span> <span id="resolveConfirmSubject"></span></p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200">
            <button type="button" onclick="closeResolveConfirmModal()"
                class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition duration-200 cursor-pointer">
                Cancel
            </button>
            <button type="button" id="resolveConfirmExecuteBtn" onclick="executeResolveConfirmed()"
                class="px-5 py-2.5 text-white font-medium rounded-lg transition duration-200 cursor-pointer focus:outline-none focus:ring-2">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
function closeResolveConfirmModal() {
    var confirmEl = document.getElementById('resolve-confirm-modal');
    if (confirmEl) confirmEl.classList.add('hidden');
    var mainEl = document.getElementById('resolve-opinion-modal');
    if (mainEl && !mainEl.classList.contains('hidden')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

function openResolveModal(documentId, trackingNumber, subjectMatter) {
    document.getElementById('resolveDocumentIdInput').value = documentId;
    document.getElementById('resolveTrackingCache').value = String(trackingNumber ?? '').trim();
    document.getElementById('resolveSubjectCache').value = String(subjectMatter ?? '').trim();
    openModal('resolve-opinion-modal');
}

function openResolveConfirmModal(resolution) {
    const id = document.getElementById('resolveDocumentIdInput').value;
    if (!id) return;

    const tracking = document.getElementById('resolveTrackingCache').value || '—';
    const subject = document.getElementById('resolveSubjectCache').value || '—';
    document.getElementById('resolvePendingResolution').value = resolution;
    document.getElementById('resolveConfirmTracking').textContent = tracking;
    document.getElementById('resolveConfirmSubject').textContent = subject;

    const titleEl = document.getElementById('resolveConfirmTitle');
    const msgEl = document.getElementById('resolveConfirmMessage');
    const btn = document.getElementById('resolveConfirmExecuteBtn');

    if (resolution === 'withdraw') {
        titleEl.textContent = 'Withdraw document?';
        msgEl.textContent = 'This will set the document status to Withdrawn. Only continue if you are sure.';
        btn.className = 'px-5 py-2.5 text-white font-medium rounded-lg transition duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-red-500 bg-red-600 hover:bg-red-700';
    } else {
        titleEl.textContent = 'Proceed to agenda?';
        msgEl.textContent = 'This will mark unfavorable offices as skipped and move the document to Ready for Agenda.';
        btn.className = 'px-5 py-2.5 text-white font-medium rounded-lg transition duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500 bg-amber-600 hover:bg-amber-700';
    }

    btn.disabled = false;
    btn.textContent = 'Confirm';

    openModal('resolve-confirm-modal');
}

function submitResolveWithdraw() {
    const id = document.getElementById('resolveDocumentIdInput').value;
    if (!id) return;
    openResolveConfirmModal('withdraw');
}

function submitResolveProceed() {
    const id = document.getElementById('resolveDocumentIdInput').value;
    if (!id) return;
    openResolveConfirmModal('proceed');
}

function postResolveOpinion(resolution) {
    const id = document.getElementById('resolveDocumentIdInput').value;
    const fd = new FormData();
    fd.append('document_id', id);
    fd.append('resolution', resolution);
    return fetch('/PangasinanLIS/includes/actions_committee/process_resolve_opinion.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); });
}

function executeResolveConfirmed() {
    const resolution = document.getElementById('resolvePendingResolution').value;
    if (resolution !== 'proceed' && resolution !== 'withdraw') return;

    const btn = document.getElementById('resolveConfirmExecuteBtn');
    btn.disabled = true;
    btn.textContent = 'Processing…';

    postResolveOpinion(resolution)
        .then(function (data) {
            if (data.status === 'success') {
                closeResolveConfirmModal();
                closeModal('resolve-opinion-modal');
                if (typeof showEndorseToast === 'function') showEndorseToast(data.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1200);
            } else {
                closeResolveConfirmModal();
                if (typeof showEndorseToast === 'function') showEndorseToast(data.message, 'error');
            }
        })
        .catch(function () {
            closeResolveConfirmModal();
            if (typeof showEndorseToast === 'function') showEndorseToast('Network error.', 'error');
        })
        .finally(function () {
            btn.disabled = false;
            btn.textContent = 'Confirm';
        });
}
</script>
