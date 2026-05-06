<?php
$stmt = $pdo->prepare("SELECT document_type_id AS id, document_type_name FROM document_types WHERE status = 'active' ORDER BY document_type_name");
$stmt->execute();
$documentTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT source_type_id AS id, source_type_name FROM source_types WHERE status = 'active' AND is_deleted = 0 ORDER BY source_type_name");
$stmt->execute();
$sourceTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT external_office_id AS id, external_office_name FROM external_offices WHERE status = 'active' ORDER BY external_office_name");
$stmt->execute();
$externalOffices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT hospital_id AS id, hospital_name FROM hospitals WHERE status = 'active' ORDER BY hospital_name");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT muni_city_id AS id, muni_city_name FROM muni_cities WHERE is_deleted = FALSE AND status = 'active' ORDER BY muni_city_name");
$stmt->execute();
$muniCities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT routing_option_id AS id, routing_option_name FROM routing_options WHERE is_deleted = FALSE AND status = 'active' ORDER BY routing_option_name");
$stmt->execute();
$routingOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT communication_category_id AS id, communication_category_name FROM communication_categories WHERE is_deleted = FALSE AND status = 'active' ORDER BY communication_category_name");
$stmt->execute();
$communicationCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build a map: document_type_id => [requirements]
$stmt = $pdo->prepare("
    SELECT dr.document_type_id, r.requirement_id, r.requirement_name
    FROM requirements r
    INNER JOIN document_requirement dr ON r.requirement_id = dr.requirement_id
    WHERE r.is_deleted = FALSE AND r.status = 1
    ORDER BY r.requirement_name
");
$stmt->execute();
$allRequirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group requirements by document_type_id
$requirementsByDocType = [];
foreach ($allRequirements as $req) {
    $requirementsByDocType[$req['document_type_id']][] = [
        'id' => $req['requirement_id'],
        'name' => $req['requirement_name'],
    ];
}
?>

<!-- Route File Modal Wrapper -->
<div id="route-file-modal" class="hidden">

    <!-- Modal Overlay -->
    <div id="modalOverlay" onclick="closeModal('route-file-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div id="modal"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-101 w-[calc(100%-2rem)] md:w-full max-w-3xl bg-white rounded-lg shadow-xl transition-all duration-300 max-h-[90vh] flex flex-col">

        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0">
            <h2 class="text-xl font-semibold text-gray-800">Route File</h2>
            <button id="closeBtn" onclick="closeModal('route-file-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="userMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium shrink-0">
            <div id="userMessageContent"></div>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="overflow-y-auto p-6 flex-1">
            <form id="userForm" method="POST" action="../../includes/actions_admin/process_route_document.php"
                enctype="multipart/form-data">

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Document
                    Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="trackingNumber" class="block text-sm font-medium text-gray-700 mb-1">Tracking Number
                            *</label>
                        <input type="text" id="trackingNumber" name="trackingNumber" value="<?php echo isset($nextTrackingNumber) ? $nextTrackingNumber : '[ERROR]'; ?>" placeholder="Enter tracking number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200 cursor-not-allowed bg-gray-100"
                            readonly required>
                    </div>
                    <div>
                        <label for="dateReceived" class="block text-sm font-medium text-gray-700 mb-1">Date Received
                            *</label>
                        <input type="date" id="dateReceived" name="dateReceived"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                            required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="subjectMatter" class="block text-sm font-medium text-gray-700 mb-1">Subject Matter
                            *</label>
                        <input type="text" id="subjectMatter" name="subjectMatter" placeholder="Enter subject matter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                            required minlength="8">
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Document
                    Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <div>
                        <label for="documentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                        <!-- Pinalitan natin yung this.value ng this lang para maipasa natin yung buong element -->
                        <select id="documentType" name="documentType"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                            required onchange="handleDocumentTypeChange(this)">
                            <option value="" disabled selected>Select a document type</option>
                            <?php if (!isset($documentTypes['error'])): ?>
                                <?php foreach ($documentTypes as $documentType): ?>
                                    <!-- Nagdagdag tayo ng data-name dito -->
                                    <option value="<?= htmlspecialchars($documentType['id']) ?>"
                                            data-name="<?= htmlspecialchars(strtolower($documentType['document_type_name'])) ?>">
                                        <?= htmlspecialchars($documentType['document_type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="sourceType" class="block text-sm font-medium text-gray-700 mb-1">Source Type
                            *</label>
                        <select id="sourceType" name="sourceType"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                            required onchange="handleSourceTypeChange(this)">
                            <option value="" disabled selected>Select a source</option>
                            <?php if (!isset($sourceTypes['error'])): ?>
                                <?php foreach ($sourceTypes as $sourceType): ?>
                                    <option value="<?= htmlspecialchars($sourceType['id']) ?>"
                                        data-name="<?= htmlspecialchars(strtolower($sourceType['source_type_name'])) ?>">
                                        <?= htmlspecialchars($sourceType['source_type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Source-type-specific fields — appear below the two dropdowns -->
                <div id="sourceFieldsContainer" class="mb-6">

                    <!-- CLIENT fields: client name + muni city -->
                    <div id="sourceField-client" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="clientName" class="block text-sm font-medium text-gray-700 mb-1">Client Name
                                *</label>
                            <input type="text" id="clientName" name="clientName" placeholder="Enter client name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                        </div>
                        <div>
                            <label for="muniCity" class="block text-sm font-medium text-gray-700 mb-1">Municipality/City
                                *</label>
                            <select id="muniCity" name="muniCity"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                                <option value="" disabled selected>Select municipality/city</option>
                                <?php if (!isset($muniCities['error'])): ?>
                                    <?php foreach ($muniCities as $muniCity): ?>
                                        <option value="<?= htmlspecialchars($muniCity['id']) ?>">
                                            <?= htmlspecialchars($muniCity['muni_city_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- SP MEMBER fields -->
                    <div id="sourceField-sp_member" class="hidden mt-4">
                        <label for="spMember" class="block text-sm font-medium text-gray-700 mb-1">SP Member Name
                            *</label>
                        <input type="text" id="spMember" name="spMember" placeholder="Enter SP member name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>

                    <!-- EXTERNAL OFFICE fields -->
                    <div id="sourceField-external_office" class="hidden mt-4">
                        <label for="externalOffice" class="block text-sm font-medium text-gray-700 mb-1">External Office
                            *</label>
                        <select id="externalOffice" name="externalOffice"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                            <option value="" disabled selected>Select an office</option>
                            <?php if (!isset($externalOffices['error'])): ?>
                                <?php foreach ($externalOffices as $externalOffice): ?>
                                    <option value="<?= htmlspecialchars($externalOffice['id']) ?>">
                                        <?= htmlspecialchars($externalOffice['external_office_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- HOSPITAL fields -->
                    <div id="sourceField-hospital" class="hidden mt-4">
                        <label for="hospital" class="block text-sm font-medium text-gray-700 mb-1">Hospital *</label>
                        <select id="hospital" name="hospital"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                            <option value="" disabled selected>Select a hospital</option>
                            <?php if (!isset($hospitals['error'])): ?>
                                <?php foreach ($hospitals as $hospital): ?>
                                    <option value="<?= htmlspecialchars($hospital['id']) ?>">
                                        <?= htmlspecialchars($hospital['hospital_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- AGENCY fields -->
                    <div id="sourceField-agency" class="hidden mt-4">
                        <label for="agency" class="block text-sm font-medium text-gray-700 mb-1">Agency Name *</label>
                        <input type="text" id="agency" name="agency" placeholder="Enter agency name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>

                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Routing Details</h3>
                <!-- BINAGO: Dinagdagan ng md:grid-cols-2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    
                    <!-- BINAGO: Nilagyan ng id="routingWrapper" at md:col-span-2 (para full width default) -->
                    <div id="routingWrapper" class="md:col-span-2 transition-all duration-300">
                        <label for="routingOption" class="block text-sm font-medium text-gray-700 mb-1">Routing Option *</label>
                        <select id="routingOption" name="routingOption"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                            required onchange="handleRoutingOptionChange(this)">
                            <option value="" disabled selected>Select where to route</option>
                            <?php if (!isset($routingOptions['error'])): ?>
                                <?php foreach ($routingOptions as $option): ?>
                                    <option value="<?= htmlspecialchars($option['id']) ?>" data-name="<?= htmlspecialchars(strtolower($option['routing_option_name'])) ?>">
                                        <?= htmlspecialchars($option['routing_option_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- COMMUNICATION CATEGORY (Naka-hidden by default) -->
                    <div id="communicationCategoryGroup" class="hidden transition-all duration-300">
                        <label for="communicationCategory" class="block text-sm font-medium text-gray-700 mb-1">Communication Category *</label>
                        <select id="communicationCategory" name="communicationCategory"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                            <option value="" disabled selected>Select Category</option>
                            <?php if (!isset($communicationCategories['error'])): ?>
                                <?php foreach ($communicationCategories as $communicationCategory): ?>
                                    <option value="<?= htmlspecialchars($communicationCategory['id']) ?>">
                                        <?= htmlspecialchars($communicationCategory['communication_category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- ===================== REQUIREMENTS SECTION ===================== -->
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Requirements (Optional)</h3>
                <div id="requirementsSection" class="mb-6">
                    <!-- PHP: pre-render all requirements per doc type as hidden groups -->
                    <?php foreach ($documentTypes as $dt): ?>
                        <?php $dtId = $dt['id']; ?>
                        <div id="req-group-<?= $dtId ?>" class="hidden">
                            <?php if (!empty($requirementsByDocType[$dtId])): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <?php foreach ($requirementsByDocType[$dtId] as $req): ?>
                                        <label
                                            class="flex items-center gap-2 p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-[#0033A1] cursor-pointer transition-colors duration-150">
                                            <input type="checkbox" name="requirements[]" value="<?= htmlspecialchars($req['id']) ?>"
                                                class="w-4 h-4 rounded border-gray-600 text-[#0033A1] focus:ring-[#0033A1] cursor-pointer">
                                            <span class="text-sm text-gray-700"><?= htmlspecialchars($req['name']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-400 text-center py-4">No requirement.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Default state: no doc type selected yet -->
                    <div id="req-placeholder"
                        class="flex items-center justify-center py-4 rounded-lg border-2 border-dashed border-gray-200 text-sm text-gray-400">
                        Select a document type to see requirements
                    </div>
                </div>
                <!-- =================== END REQUIREMENTS SECTION =================== -->

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Additional
                    Information</h3>
                <div class="grid gap-4 mb-2">
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks/Notes (Optional)</label>
                        <div>
                            <textarea id="remarks" name="remarks" placeholder="Enter additional remarks or notes"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                                style="resize: vertical; overflow: auto; min-height: 80px; max-height: 80px; width: 100%;"></textarea>
                        </div>
                    </div>

                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                    <div class="flex flex-col items-center justify-center min-h-64">
                        <div id="dropzone"
                            class="w-full border-2 border-dashed border-gray-300 rounded-2xl p-10 flex flex-col items-center justify-center gap-4 cursor-pointer transition-all duration-200 bg-white hover:border-blue-400 hover:bg-blue-50"
                            ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                            ondrop="handleDrop(event)" onclick="document.getElementById('fileInput').click()">
                            <div id="uploadIcon" class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-gray-700">Drag &amp; drop files here</p>
                                    <p class="text-xs text-gray-400 mt-1">or <span
                                            class="text-blue-500 underline">browse</span></p>
                                </div>
                                <p class="text-xs text-gray-400">PNG, JPG, PDF, DOCX, XLSX — up to 10MB</p>
                            </div>
                            <input type="file" name="attachments[]" id="fileInput" class="hidden" multiple
                                onchange="handleFileInput(event)" />
                        </div>

                        <!-- File list - full width -->
                        <ul id="fileList" class="w-full mt-4 flex flex-col gap-2"></ul>
                    </div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button type="button" id="cancelBtn" onclick="closeModal('route-file-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button type="submit" form="userForm" id="addBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Route File
            </button>
        </div>
    </div>
</div>

<script>
    // ============================================================
    //  SOURCE TYPE – dynamic right-side fields
    // ============================================================
    const SOURCE_TYPE_MAP = [
        { pattern: /\bclient\b/, panel: 'sourceField-client' },
        { pattern: /\bsp\b/, panel: 'sourceField-sp_member' },
        { pattern: /\bexternal\b/, panel: 'sourceField-external_office' },
        { pattern: /\bhospital\b/, panel: 'sourceField-hospital' },
        { pattern: /\bagency\b/, panel: 'sourceField-agency' },
    ];

    function handleSourceTypeChange(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const sourceName = (selectedOption.dataset.name || '').trim().toLowerCase();

        document.querySelectorAll('[id^="sourceField-"]').forEach(el => {
            el.classList.add('hidden');
        });

        const match = SOURCE_TYPE_MAP.find(entry => entry.pattern.test(sourceName));

        if (match) {
            const panel = document.getElementById(match.panel);
            if (panel) {
                panel.classList.remove('hidden');
            }
        }
    }

    // ============================================================
    //  DOCUMENT TYPE – dynamic requirements checklist & Routing filter
    // ============================================================
    function handleDocumentTypeChange(selectEl) {
        const docTypeId = selectEl.value;
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const docTypeName = (selectedOption.dataset.name || '').trim().toLowerCase();

        // 1. Handle Requirements UI
        document.getElementById('req-placeholder').classList.add('hidden');
        document.querySelectorAll('[id^="req-group-"]').forEach(el => {
            el.classList.add('hidden');
        });
        document.querySelectorAll('input[name="requirements[]"]').forEach(cb => {
            cb.checked = false;
        });

        if (docTypeId) {
            const group = document.getElementById('req-group-' + docTypeId);
            if (group) {
                group.classList.remove('hidden');
            }
        } else {
            document.getElementById('req-placeholder').classList.remove('hidden');
        }

        // 2. Handle "Noted" Visibility sa Routing Option
        const routingSelect = document.getElementById('routingOption');
        let notedOption = null;

        // Hanapin kung aling option ang "Noted"
        Array.from(routingSelect.options).forEach(opt => {
            if ((opt.dataset.name || '').includes('noted')) {
                notedOption = opt;
            }
        });

        if (notedOption) {
            // Kung ang Document Type ay may salitang "communication"
            if (docTypeName.includes('communication')) {
                notedOption.classList.remove('hidden');
                notedOption.disabled = false;
            } else {
                // Kung HINDI communication, itago at i-disable ang "Noted"
                notedOption.classList.add('hidden');
                notedOption.disabled = true;

                // Kung sakaling naka-select ang "Noted", i-reset natin ang dropdown
                if (routingSelect.value === notedOption.value) {
                    routingSelect.value = ''; 
                    handleRoutingOptionChange(routingSelect); // I-trigger ang pagtago ng Comm Category
                }
            }
        }
    }

    // ============================================================
    //  ROUTING OPTION – dynamic communication category
    // ============================================================
    function handleRoutingOptionChange(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const optionName = (selectedOption.dataset.name || '').trim().toLowerCase();
        
        const commCategoryGroup = document.getElementById('communicationCategoryGroup');
        const commCategorySelect = document.getElementById('communicationCategory');
        const routingWrapper = document.getElementById('routingWrapper'); // Kukunin natin wrapper

        // I-check kung ang pinili ay may salitang "noted"
        if (optionName.includes('noted')) {
            commCategoryGroup.classList.remove('hidden');
            commCategorySelect.required = true; 
            routingWrapper.classList.remove('md:col-span-2'); // Tatanggalin ang full width para magtabi sila
        } else {
            commCategoryGroup.classList.add('hidden');
            commCategorySelect.required = false; 
            commCategorySelect.value = ''; 
            routingWrapper.classList.add('md:col-span-2'); // Babalik sa full width kung walang category
        }
    }

    // ============================================================
    //  MODAL RESET on open
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        const modalId = 'route-file-modal';

        // Hide "Noted" sa initial load bago pa man magbukas ang modal
        const routingSelect = document.getElementById('routingOption');
        if (routingSelect) {
            Array.from(routingSelect.options).forEach(opt => {
                if ((opt.dataset.name || '').includes('noted')) {
                    opt.classList.add('hidden');
                    opt.disabled = true;
                }
            });
        }

        const originalOpenModal = window.openModal;
        window.openModal = function (id) {
            if (id === modalId) {
                // Reset the form
                const form = document.getElementById('userForm');
                if (form) form.reset();

                // Reset source type UI
                document.querySelectorAll('[id^="sourceField-"]').forEach(el => {
                    el.classList.add('hidden');
                });

                // Reset routing option UI & Communication Category
                if (routingSelect) {
                    routingSelect.value = '';
                    Array.from(routingSelect.options).forEach(opt => {
                        if ((opt.dataset.name || '').includes('noted')) {
                            opt.classList.add('hidden');
                            opt.disabled = true;
                        }
                    });
                }

                const commCategoryGroup = document.getElementById('communicationCategoryGroup');
                if (commCategoryGroup) {
                    commCategoryGroup.classList.add('hidden');
                    document.getElementById('communicationCategory').required = false;
                    document.getElementById('communicationCategory').value = '';
                    
                    // Ibalik din natin sa full width yung Routing Option
                    const routingWrapper = document.getElementById('routingWrapper');
                    if (routingWrapper) routingWrapper.classList.add('md:col-span-2');
                }

                // Reset requirements UI
                document.querySelectorAll('[id^="req-group-"]').forEach(el => {
                    el.classList.add('hidden');
                });
                document.getElementById('req-placeholder').classList.remove('hidden');

                // Hide message if any
                const msg = document.getElementById('userMessage');
                if (msg) msg.classList.add('hidden');

                // Reset file upload
                selectedFiles = [];
                const fileList = document.getElementById('fileList');
                if (fileList) fileList.innerHTML = '';
            }
            if (originalOpenModal) {
                originalOpenModal.call(this, id);
            }
        };
    });

    // ============================================================
    //  DRAG & DROP FILE UPLOAD
    // ============================================================
    let selectedFiles = [];

    function handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        const dropzone = document.getElementById('dropzone');
        dropzone.classList.add('border-blue-400', 'bg-blue-50');
        dropzone.classList.remove('border-gray-300');
    }

    function handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        const dropzone = document.getElementById('dropzone');
        dropzone.classList.remove('border-blue-400', 'bg-blue-50');
        dropzone.classList.add('border-gray-300');
    }

    function handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        const dropzone = document.getElementById('dropzone');
        dropzone.classList.remove('border-blue-400', 'bg-blue-50');
        dropzone.classList.add('border-gray-300');

        const files = Array.from(event.dataTransfer.files);
        addFiles(files);
    }

    function handleFileInput(event) {
        const files = Array.from(event.target.files);
        event.target.value = ''; 
        addFiles(files);
    }

    function addFiles(files) {
        const allowed = [
            'image/png', 'image/jpeg', 'image/jpg', 'application/pdf',
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        ];
        const maxSize = 10 * 1024 * 1024; // 10MB

        files.forEach(file => {
            if (!allowed.includes(file.type)) {
                alert(`"${file.name}" is not allowed. Only PNG, JPG, PDF, DOCX, and XLSX files are accepted.`);
                return;
            }
            if (file.size > maxSize) {
                alert(`"${file.name}" exceeds the 10MB limit.`);
                return;
            }
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                selectedFiles.push(file);
            }
        });

        renderFileList();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        renderFileList();
    }

    function getFileIcon(type) {
        const wordTypes = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const excelTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (type === 'application/pdf') {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>`;
        }
        if (wordTypes.includes(type)) {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>`;
        }
        if (excelTypes.includes(type)) {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0118 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0118 9.375v1.5m1.5-3.75C19.496 8.25 20 8.754 20 9.375v6.75C20 16.996 19.496 17.5 18.875 17.5m-12.75 0h12.75m-12.75 0C5.504 17.5 5 16.996 5 16.375v-6.75C5 8.754 5.504 8.25 6.125 8.25" />
        </svg>`;
        }
        return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
    </svg>`;
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function renderFileList() {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';

        if (selectedFiles.length === 0) return;

        selectedFiles.forEach((file, index) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm';
            li.innerHTML = `
            <div class="flex items-center gap-3 min-w-0">
                ${getFileIcon(file.type)}
                <div class="min-w-0">
                    <p class="font-medium text-gray-700 truncate">${file.name}</p>
                    <p class="text-xs text-gray-400">${formatBytes(file.size)}</p>
                </div>
            </div>
            <button
                type="button"
                onclick="removeFile(${index})"
                class="shrink-0 text-gray-400 hover:text-red-500 transition-colors cursor-pointer"
                title="Remove file">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
            fileList.appendChild(li);
        });
    }

    // ============================================================
    //  AJAX FORM SUBMISSION - The Ultimate Fix for Files
    // ============================================================
    const userForm = document.getElementById('userForm');

    if (userForm) {
        userForm.addEventListener('submit', function (e) {
            e.preventDefault(); 

            const formData = new FormData(this);
            formData.delete('attachments[]');

            if (selectedFiles.length > 0) {
                selectedFiles.forEach(file => {
                    formData.append('attachments[]', file);
                });
            }

            const submitBtn = document.getElementById('addBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Routing...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                redirect: 'manual' 
            })
            .then(response => {
                window.location.href = '../../pages/admin/routed_documents';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while routing the document.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
</script>