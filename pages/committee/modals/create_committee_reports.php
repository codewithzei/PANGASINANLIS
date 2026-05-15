<?php 
// 1. KUNIN ANG MGA COMMITTEES
$committees = [];
try {
    $q = $pdo->query("SELECT committee_id, committee_name FROM committees WHERE COALESCE(is_deleted, 0) = 0 AND status = 'active' ORDER BY committee_name ASC");
    if ($q) {
        $committees = $q->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $committees = [];
}

// 2. KUNIN ANG MGA REPORT TYPES
$reportTypes = [];
try {
    $stmtReportTypes = $pdo->prepare("
        SELECT report_type_id, report_type_name
        FROM report_types
        WHERE COALESCE(is_deleted, 0) = 0 AND status = 'active'
        ORDER BY CASE WHEN LOWER(report_type_name) LIKE '%joint%' THEN 1 ELSE 0 END ASC,
                 report_type_id ASC
    ");
    $stmtReportTypes->execute();
    $reportTypes = $stmtReportTypes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reportTypes = [];
}
?>

<!-- ============================================================
     MODAL: CREATE COMMITTEE REPORT
============================================================= -->
<div id="create-report-modal" class="hidden">

    <!-- Modal Overlay -->
    <div id="reportModalOverlay" onclick="closeReportModal()"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal Panel -->
    <div id="reportModal"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-3xl bg-white rounded-lg shadow-xl transition-all duration-300 max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0">
            <div>
                <h2 class="text-xl font-semibold text-gray-800" id="modal-title">Create Committee Report</h2>
                <p class="text-sm text-gray-500 mt-1">Draft a report for approved documents to be submitted to Plenary.</p>
            </div>
            <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="overflow-y-auto p-6 flex-1 custom-scrollbar">
            <form id="committeeReportForm" class="space-y-6">
                
                <!-- SECTION 1: Select Approved Documents -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-semibold text-gray-800">Select Approved Documents</h4>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full" id="modalSelectedBadge">0 Selected</span>
                    </div>

                    <!-- Search Box -->
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                        </div>
                        <input type="text" id="modalDocSearch" placeholder="Search approved documents..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 text-sm" oninput="filterModalDocs(this.value)">
                    </div>

                    <!-- Scrollable Document List -->
                    <div class="max-h-52 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 mb-4" id="modalDocList">
                        <?php if (!empty($approvedHearings)): ?>
                            <?php foreach ($approvedHearings as $doc): ?>
                                <label class="flex items-start gap-3 p-3 hover:bg-gray-50 cursor-pointer transition-colors modal-doc-item">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <input type="checkbox" name="selected_docs[]" value="<?php echo htmlspecialchars($doc['document_id']); ?>" data-tracking="<?php echo htmlspecialchars($doc['tracking_number']); ?>" class="modal-doc-checkbox w-4 h-4 text-[#0033A1] bg-white border-gray-300 rounded focus:ring-[#0033A1] cursor-pointer" onchange="updateModalSelection()">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm font-semibold text-gray-800 truncate doc-tracking"><?php echo htmlspecialchars($doc['tracking_number']); ?></span>
                                            <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded">Cycle <?php echo htmlspecialchars($doc['cycle'] ?? '1'); ?></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1 truncate doc-subject"><?php echo htmlspecialchars($doc['subject_matter']); ?></p>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider"><?php echo htmlspecialchars($doc['chairperson'] ?? 'Committee'); ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-sm text-gray-500 italic">No approved documents available.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Selected Document Pills -->
                    <div class="flex flex-wrap gap-2 empty:hidden" id="modalSelectedPills"></div>
                </div>

                <!-- SECTION 2: Report Details -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                    <h4 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-2">Report Details</h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Title *</label>
                        <input type="text" name="report_title" required placeholder="e.g., Committee Report on the Proposed Ordinance..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>

                    <!-- CUSTOM MULTI-SELECT DROPDOWN -->
                    <div class="relative" id="committeeDropdownWrapper">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Committee In-Charge *</label>
                        
                        <!-- Simulated Select Box (Button) -->
                        <div id="committeeSelectButton" onclick="toggleCommitteeDropdown()" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm text-gray-700 cursor-pointer flex justify-between items-center transition duration-200 hover:border-gray-400">
                            <span id="committeeSelectLabel" class="truncate pr-4 text-gray-500">Select committee(s)...</span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Dropdown List with Checkboxes -->
                        <div id="committeeDropdownList" class="hidden absolute z-[105] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto custom-scrollbar">
                            <div class="p-2 space-y-1">
                                <?php if (!empty($committees)): ?>
                                    <?php foreach ($committees as $c): ?>
                                    <label class="flex items-start gap-2 p-2 hover:bg-blue-50 rounded-lg cursor-pointer transition-colors duration-150">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <input type="checkbox" name="committee_id[]" value="<?php echo (int) $c['committee_id']; ?>" data-name="<?php echo htmlspecialchars($c['committee_name']); ?>" 
                                                class="committee-checkbox w-4 h-4 text-[#0033A1] bg-white border-gray-300 rounded focus:ring-[#0033A1] cursor-pointer" onchange="updateCommitteeLabel()">
                                        </div>
                                        <span class="text-sm text-gray-700 flex-1 leading-snug"><?php echo htmlspecialchars($c['committee_name']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-2 text-sm text-gray-500 italic text-center">No committees available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Type *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <?php if (!empty($reportTypes)): ?>
                                <?php
                                $defaultReportTypeId = null;
                                foreach ($reportTypes as $t) {
                                    if (stripos((string) ($t['report_type_name'] ?? ''), 'joint') === false) {
                                        $defaultReportTypeId = (int) $t['report_type_id'];
                                        break;
                                    }
                                }
                                if ($defaultReportTypeId === null) {
                                    $defaultReportTypeId = (int) ($reportTypes[0]['report_type_id'] ?? 0);
                                }
                                ?>
                                <?php foreach ($reportTypes as $type): ?>
                                    <?php
                                    $tid = (int) $type['report_type_id'];
                                    $isJoint = stripos((string) ($type['report_type_name'] ?? ''), 'joint') !== false;
                                    $checked = ($tid === $defaultReportTypeId);
                                    ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="report_type_id" value="<?php echo htmlspecialchars((string) $tid); ?>" class="peer sr-only" <?php echo $checked ? 'checked' : ''; ?> data-name="<?php echo htmlspecialchars(strtolower((string) ($type['report_type_name'] ?? ''))); ?>" data-is-joint="<?php echo $isJoint ? '1' : '0'; ?>" onchange="toggleJointReportNumber()">
                                        <div class="text-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 peer-checked:border-[#0033A1] peer-checked:text-[#0033A1] peer-checked:bg-blue-50 transition-all hover:bg-gray-50 flex items-center justify-center h-full">
                                            <?php echo htmlspecialchars($type['report_type_name']); ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-2 text-sm text-gray-500 italic p-2 border border-gray-200 rounded-lg bg-gray-50">
                                    No report types available in database.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dynamic Report Numbers -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="reportNumbersWrapper">
                        <div class="w-full md:col-span-2 transition-all duration-300" id="committeeReportNumberWrapper">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Committee Report Number *</label>
                            <input type="text" name="report_number" required placeholder="CR-2026-046" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                        </div>

                        <div id="jointReportNumberContainer" class="hidden w-full transition-all duration-300">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Joint Committee Report Number *</label>
                            <input type="text" id="joint_report_number" name="joint_report_number" placeholder="JCR-2026-001" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Approval *</label>
                        <input type="date" name="date_of_approval" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 text-gray-700">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Summary of Findings *</label>
                        <textarea name="summary_of_findings" rows="4" required placeholder="Enter the findings, recommendations, and conclusion of the committee..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 resize-y"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button type="button" id="cancelBtn" onclick="closeReportModal()"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button type="submit" form="committeeReportForm" id="saveReportBtn"
                class="px-5 py-2.5 text-white bg-[#0033A1] hover:bg-blue-800 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Save & Generate Report
            </button>
        </div>

    </div>
</div>

<script>
    // ============================================================
    // MODAL LOGIC: CREATE COMMITTEE REPORT
    // ============================================================

    // 1. Joint Report Toggler
    function toggleJointReportNumber() {
        const selectedRadio = document.querySelector('input[name="report_type_id"]:checked');
        const jointContainer = document.getElementById('jointReportNumberContainer');
        const jointInput = document.getElementById('joint_report_number');
        const crnWrapper = document.getElementById('committeeReportNumberWrapper');

        if (selectedRadio) {
            const isJoint = selectedRadio.getAttribute('data-is-joint') === '1';
            const typeName = selectedRadio.getAttribute('data-name') || '';

            if (isJoint || typeName.includes('joint')) {
                jointContainer.classList.remove('hidden');
                jointInput.required = true;
                crnWrapper.classList.remove('md:col-span-2');
            } else {
                jointContainer.classList.add('hidden');
                jointInput.required = false;
                jointInput.value = ''; 
                crnWrapper.classList.add('md:col-span-2');
            }
        }
    }

    // 2. Custom Committee Dropdown Logic
    function toggleCommitteeDropdown() {
        const list = document.getElementById('committeeDropdownList');
        const btn = document.getElementById('committeeSelectButton');
        
        list.classList.toggle('hidden');
        
        if (!list.classList.contains('hidden')) {
            btn.classList.add('ring-2', 'ring-[#0033A1]', 'border-transparent');
            btn.classList.remove('border-gray-300');
        } else {
            btn.classList.remove('ring-2', 'ring-[#0033A1]', 'border-transparent');
            btn.classList.add('border-gray-300');
        }
    }

    function updateCommitteeLabel() {
        const checkboxes = document.querySelectorAll('.committee-checkbox:checked');
        const label = document.getElementById('committeeSelectLabel');
        
        if (checkboxes.length === 0) {
            label.textContent = 'Select committee(s)...';
            label.classList.add('text-gray-500');
            label.classList.remove('text-gray-800', 'font-medium');
        } else if (checkboxes.length === 1) {
            label.textContent = checkboxes[0].getAttribute('data-name');
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-800', 'font-medium');
        } else {
            label.textContent = checkboxes.length + ' committees selected';
            label.classList.remove('text-gray-500');
            label.classList.add('text-gray-800', 'font-medium');
        }
    }

    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('committeeDropdownWrapper');
        const list = document.getElementById('committeeDropdownList');
        const btn = document.getElementById('committeeSelectButton');
        
        if (wrapper && !wrapper.contains(event.target)) {
            if (list && !list.classList.contains('hidden')) {
                list.classList.add('hidden');
                btn.classList.remove('ring-2', 'ring-[#0033A1]', 'border-transparent');
                btn.classList.add('border-gray-300');
            }
        }
    });

    // 3. Main Modal Actions
    function processCommitteeReport() {
        // Kunin yung mga naka-check sa main table
        const mainSelected = Array.from(document.querySelectorAll('.approved-checkbox:checked')).map(cb => cb.value);

        // I-sync yung checks sa loob ng modal list
        const modalCheckboxes = document.querySelectorAll('.modal-doc-checkbox');
        modalCheckboxes.forEach(cb => {
            cb.checked = mainSelected.includes(cb.value);
            highlightModalRow(cb);
        });

        updateModalSelection(); 
        toggleJointReportNumber(); 
        
        // Ipakita ang modal
        const modal = document.getElementById('create-report-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; 
        }
    }

    function closeReportModal() {
        const modal = document.getElementById('create-report-modal');
        const form = document.getElementById('committeeReportForm');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            document.querySelectorAll('.committee-checkbox').forEach(cb => cb.checked = false);
            updateCommitteeLabel();

            if (form) {
                form.reset();
            }
            toggleJointReportNumber();
        }
    }

    function syncApprovedCheckboxesFromModal() {
        document.querySelectorAll('.modal-doc-checkbox').forEach(function(cb) {
            const main = document.querySelector('.approved-checkbox[value="' + String(cb.value) + '"]');
            if (main) {
                main.checked = cb.checked;
            }
        });
        if (typeof updateApprovedSelection === 'function') {
            updateApprovedSelection();
        }
    }

    // 4. Modal Documents UI Functions
    function updateModalSelection() {
        const checkboxes = document.querySelectorAll('.modal-doc-checkbox:checked');
        const badge = document.getElementById('modalSelectedBadge');
        const pillContainer = document.getElementById('modalSelectedPills');

        if(badge) badge.textContent = `${checkboxes.length} Selected`;

        if(pillContainer) {
            pillContainer.innerHTML = '';
            checkboxes.forEach(cb => {
                highlightModalRow(cb);

                const tracking = cb.getAttribute('data-tracking');
                const pill = document.createElement('div');
                pill.className = 'inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 text-xs font-bold rounded-full';
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-gray-400 hover:text-red-500 focus:outline-none transition-colors ml-1 cursor-pointer';
                removeBtn.title = 'Remove';
                removeBtn.setAttribute('aria-label', 'Remove document');
                const docId = String(cb.value);
                removeBtn.addEventListener('click', function() {
                    uncheckModalDoc(docId);
                });
                removeBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
                pill.appendChild(document.createTextNode(tracking || ''));
                pill.appendChild(removeBtn);
                pillContainer.appendChild(pill);
            });
        }

        syncApprovedCheckboxesFromModal();
    }

    function uncheckModalDoc(val) {
        const cb = document.querySelector('.modal-doc-checkbox[value="' + String(val) + '"]');
        if (cb) {
            cb.checked = false;
            highlightModalRow(cb);
            updateModalSelection();
        }
    }

    function highlightModalRow(checkbox) {
        const row = checkbox.closest('label');
        if (row) {
            if (checkbox.checked) {
                row.classList.add('bg-blue-50');
                row.classList.remove('hover:bg-gray-50');
            } else {
                row.classList.remove('bg-blue-50');
                row.classList.add('hover:bg-gray-50');
            }
        }
    }

    function filterModalDocs(query) {
        query = query.toLowerCase();
        const items = document.querySelectorAll('.modal-doc-item');
        items.forEach(item => {
            const tracking = item.querySelector('.doc-tracking').textContent.toLowerCase();
            const subject = item.querySelector('.doc-subject').textContent.toLowerCase();
            if (tracking.includes(query) || subject.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleJointReportNumber();

        const form = document.getElementById('committeeReportForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const docChecked = document.querySelectorAll('.modal-doc-checkbox:checked');
                if (docChecked.length === 0) {
                    alert('Select at least one approved document.');
                    return;
                }

                const comChecked = document.querySelectorAll('.committee-checkbox:checked');
                if (comChecked.length === 0) {
                    alert('Select at least one committee in charge.');
                    return;
                }

                const selectedRadio = document.querySelector('input[name="report_type_id"]:checked');
                if (selectedRadio && selectedRadio.getAttribute('data-is-joint') === '1') {
                    const ji = document.getElementById('joint_report_number');
                    if (!ji || !ji.value.trim()) {
                        alert('Joint committee report number is required.');
                        return;
                    }
                }

                const btn = document.getElementById('saveReportBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Saving...';
                }

                const fd = new FormData(form);
                fetch('/PangasinanLIS/includes/actions_committee/process_create_committee_report.php', {
                    method: 'POST',
                    body: fd
                })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            window.location.href = '/PangasinanLIS/pages/committee/all_reports?report_created=1';
                            return;
                        } else {
                            alert(data.message || 'Unable to save the report.');
                        }
                    })
                    .catch(function() {
                        alert('Network error. Please try again.');
                    })
                    .finally(function() {
                        if (btn) {
                            btn.disabled = false;
                            btn.textContent = 'Save & Generate Report';
                        }
                    });
            });
        }
    });
</script>