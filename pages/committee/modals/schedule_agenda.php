<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db.php';
}
$scheduleCommittees = [];
try {
    $q = $pdo->query("SELECT committee_id, committee_name FROM committees WHERE COALESCE(is_deleted, 0) = 0 AND status = 'active' ORDER BY committee_name ASC");
    if ($q) {
        $scheduleCommittees = $q->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $scheduleCommittees = [];
}
?>
<div id="schedule-agenda-modal" class="hidden">
    <div id="scheduleAgendaModalOverlay" onclick="closeModal('schedule-agenda-modal')"
        class="fixed inset-0 bg-black/75 z-[102] transition-opacity duration-300 cursor-pointer"></div>

    <div id="scheduleAgendaModal"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[102] w-[calc(100%-2rem)] md:w-full max-w-lg bg-white rounded-lg shadow-xl transition-all duration-300 flex flex-col max-h-[90vh]">

        <div class="flex items-start justify-between gap-3 p-5 border-b border-gray-200 shrink-0">
            <div class="min-w-0 flex-1 pr-2">
                <h2 class="text-xl font-bold text-gray-800">Schedule agenda</h2>
                <p id="scheduleAgendaDocDisplay" class="mt-1 text-xs font-semibold text-[#0033A1] leading-snug wrap-break-word"></p>
            </div>
            <button type="button" onclick="closeModal('schedule-agenda-modal')"
                class="shrink-0 text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto p-6 flex-1">
            <form id="scheduleAgendaForm" method="POST" action="/PangasinanLIS/includes/actions_committee/process_schedule_agenda.php">
                <input type="hidden" id="scheduleAgendaDocumentId" name="document_id" value="">

                <div class="mb-4">
                    <label for="scheduleCommitteeId" class="block text-sm font-medium text-gray-700 mb-1">Committee *</label>
                    <select id="scheduleCommitteeId" name="committee_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                        <option value="">Select committee</option>
                        <?php foreach ($scheduleCommittees as $c): ?>
                            <option value="<?php echo (int) $c['committee_id']; ?>"><?php echo htmlspecialchars($c['committee_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="scheduleAgendaNumber" class="block text-sm font-medium text-gray-700 mb-1">Agenda No. *</label>
                        <input type="text" id="scheduleAgendaNumber" name="agenda_number" placeholder="Ex. AG-2026-01-01" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                    </div>
                    <div>
                        <label for="scheduleAgendaType" class="block text-sm font-medium text-gray-700 mb-1">Agenda Type *</label>
                        <input type="text" id="scheduleAgendaType" name="agenda_type" placeholder="Ex. Regular Session" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="scheduleChairperson" class="block text-sm font-medium text-gray-700 mb-1">Chairperson *</label>
                    <input type="text" id="scheduleChairperson" name="chairperson" placeholder="Ex. Hon. Maria Santos" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                </div>

                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="scheduleAgendaDate" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" id="scheduleAgendaDate" name="agenda_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                    </div>
                    <div>
                        <label for="scheduleAgendaTime" class="block text-sm font-medium text-gray-700 mb-1">Time *</label>
                        <input type="time" id="scheduleAgendaTime" name="agenda_time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="scheduleVenue" class="block text-sm font-medium text-gray-700 mb-1">Venue *</label>
                    <input type="text" id="scheduleVenue" name="venue" placeholder="Ex. Committee Room 1"z required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent">
                </div>

                <div class="mb-2">
                    <label for="scheduleAgendaNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes / remarks</label>
                    <textarea id="scheduleAgendaNotes" name="notes" rows="3" placeholder="Any instructions or remarks for this hearing..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                        style="resize: vertical; min-height: 72px; max-height: 160px;"></textarea>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button type="button" onclick="closeModal('schedule-agenda-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 cursor-pointer">Cancel</button>
            <button type="button" id="confirmScheduleAgendaBtn" onclick="submitScheduleAgendaForm()"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 cursor-pointer flex items-center gap-2">Save agenda</button>
        </div>
    </div>
</div>

<script>
function openScheduleAgendaModal(documentId, trackingNumber, subjectMatter) {
    const form = document.getElementById('scheduleAgendaForm');
    if (form) form.reset();
    document.getElementById('scheduleAgendaDocumentId').value = documentId;
    const tn = String(trackingNumber ?? '').trim();
    const sm = String(subjectMatter ?? '').trim();
    const line = [tn ? 'TN: ' + tn : '', sm].filter(Boolean).join(' — ');
    const el = document.getElementById('scheduleAgendaDocDisplay');
    el.textContent = line || ('Document #' + documentId);
    el.title = el.textContent;

    const btn = document.getElementById('confirmScheduleAgendaBtn');
    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Save agenda';
    }
    openModal('schedule-agenda-modal');
}

function submitScheduleAgendaForm() {
    const form = document.getElementById('scheduleAgendaForm');
    if (!form.reportValidity()) return;

    const btn = document.getElementById('confirmScheduleAgendaBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-pulse">Saving…</span>';

    const fd = new FormData(form);
    fetch(form.action, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            closeModal('schedule-agenda-modal');
            if (data.status === 'success') {
                if (typeof showEndorseToast === 'function') showEndorseToast(data.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1200);
            } else {
                if (typeof showEndorseToast === 'function') showEndorseToast(data.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Save agenda';
            }
        })
        .catch(function () {
            closeModal('schedule-agenda-modal');
            if (typeof showEndorseToast === 'function') showEndorseToast('Network error. Please try again.', 'error');
        });
}
</script>
