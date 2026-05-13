<div id="upload-compliance-modal" class="hidden">
    
    <div 
        id="complianceModalOverlay" 
        onclick="closeModal('upload-compliance-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <div 
        id="complianceModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-lg bg-white rounded-lg shadow-xl transition-all duration-300 flex flex-col max-h-[90vh]">
        
        <div class="flex items-start justify-between gap-3 p-5 border-b border-gray-200 shrink-0">
            <div class="min-w-0 flex-1 pr-2">
                <h2 class="text-xl font-bold text-gray-800">Upload Compliance</h2>
                <p id="complianceOfficeNameDisplay" class="mt-1 text-base font-semibold text-[#0033A1] leading-snug wrap-break-word"></p>
            </div>
            <button 
                type="button"
                onclick="closeModal('upload-compliance-modal')"
                class="shrink-0 text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto p-6 flex-1">
            <form id="uploadComplianceForm" method="POST" action="../../includes/actions_committee/process_upload_compliance.php" enctype="multipart/form-data">
                
                <input type="hidden" id="complianceDocumentIdInput" name="document_id">
                <input type="hidden" id="complianceEndorsementIdInput" name="endorsement_id">

                <div class="mb-5 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Submitting compliance will send the document back to 
                        <strong id="complianceOfficeNameBanner" class="font-bold text-[#0033A1]"></strong> 
                        for 2nd endorsement. They will need to upload a new opinion.
                    </p>
                </div>

                <div class="mb-5">
                    <label for="complianceDescription" class="block text-sm font-medium text-gray-700 mb-1">
                        Compliance Description (Optional)
                    </label>
                    <textarea 
                        id="complianceDescription"
                        name="compliance_description"
                        placeholder="Describe how the unfavorable remarks were addressed..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200"
                        style="resize: vertical; min-height: 100px; max-height: 200px;"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Compliance File *</label>
                    <div class="flex flex-col items-center justify-center">
                        <div id="complianceDropzone"
                            class="w-full border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center gap-4 cursor-pointer transition-all duration-200 bg-white hover:border-blue-400 hover:bg-blue-50"
                            ondragover="handleComplianceDragOver(event)" ondragleave="handleComplianceDragLeave(event)"
                            ondrop="handleComplianceDrop(event)" onclick="document.getElementById('complianceFileInput').click()">
                            
                            <div id="complianceUploadIcon" class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-gray-700">Click to upload compliance file</p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOCX, or image files up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        <input type="file" name="compliance_file" id="complianceFileInput" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            onchange="handleComplianceFileInput(event)" />
                    </div>

                    <ul id="complianceFileList" class="w-full mt-3 flex flex-col gap-2"></ul>
                </div>

            </form>
        </div>

        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button 
                type="button"
                onclick="closeModal('upload-compliance-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="button"
                id="confirmUploadComplianceBtn"
                onclick="submitComplianceForm()"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer flex items-center gap-2">
                Submit Compliance
            </button>
        </div>
    </div>
</div>


<script>
    // ============================================================
//  COMPLIANCE MODAL LOGIC
// ============================================================

let complianceSelectedFile = null;

function openComplianceModal(documentId, endorsementId, officeName) {
    // Reset Form
    const form = document.getElementById('uploadComplianceForm');
    if (form) form.reset();

    // Reset File Dropzone
    complianceSelectedFile = null;
    document.getElementById('complianceFileList').innerHTML = '';
    document.getElementById('complianceDropzone').classList.remove('hidden');

    // Set Hidden Inputs
    document.getElementById('complianceDocumentIdInput').value = documentId;
    document.getElementById('complianceEndorsementIdInput').value = endorsementId;
    
    const displayOffice = String(officeName ?? '').trim() || 'Office';
    const headerEl = document.getElementById('complianceOfficeNameDisplay');
    const bannerEl = document.getElementById('complianceOfficeNameBanner');
    headerEl.textContent = displayOffice;
    headerEl.title = displayOffice;
    bannerEl.textContent = displayOffice;
    bannerEl.title = displayOffice;

    // Reset Submit Button
    const btn = document.getElementById('confirmUploadComplianceBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = 'Submit Compliance';
    }

    openModal('upload-compliance-modal');
}

// ============================================================
//  COMPLIANCE DRAG & DROP LOGIC
// ============================================================

function handleComplianceDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropzone = document.getElementById('complianceDropzone');
    dropzone.classList.add('border-blue-400', 'bg-blue-50');
    dropzone.classList.remove('border-gray-300');
}

function handleComplianceDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropzone = document.getElementById('complianceDropzone');
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
    dropzone.classList.add('border-gray-300');
}

function handleComplianceDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropzone = document.getElementById('complianceDropzone');
    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
    dropzone.classList.add('border-gray-300');

    if (event.dataTransfer.files.length > 0) {
        addComplianceFile(event.dataTransfer.files[0]);
    }
}

function handleComplianceFileInput(event) {
    if (event.target.files.length > 0) {
        addComplianceFile(event.target.files[0]);
    }
    event.target.value = ''; 
}

function addComplianceFile(file) {
    const allowed = [
        'image/png', 'image/jpeg', 'image/jpg', 'application/pdf',
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    const maxSize = 10 * 1024 * 1024; // 10MB

    if (!allowed.includes(file.type)) {
        alert(`"${file.name}" is not allowed. Please upload a PDF, Word Doc, or Image.`);
        return;
    }
    if (file.size > maxSize) {
        alert(`"${file.name}" exceeds the 10MB limit.`);
        return;
    }

    complianceSelectedFile = file;
    renderComplianceFileList();
}

function removeComplianceFile() {
    complianceSelectedFile = null;
    renderComplianceFileList();
}

// Formatters
function getComplianceFileIcon(type) {
    const wordTypes = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (type === 'application/pdf') {
        return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>`;
    }
    if (wordTypes.includes(type)) {
        return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>`;
    }
    return `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>`;
}

function formatComplianceBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function renderComplianceFileList() {
    const fileList = document.getElementById('complianceFileList');
    const dropzone = document.getElementById('complianceDropzone');
    
    fileList.innerHTML = '';

    if (!complianceSelectedFile) {
        dropzone.classList.remove('hidden');
        return;
    }

    dropzone.classList.add('hidden');
    
    const li = document.createElement('li');
    li.className = 'flex items-center justify-between gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm';
    li.innerHTML = `
        <div class="flex items-center gap-3 min-w-0">
            ${getComplianceFileIcon(complianceSelectedFile.type)}
            <div class="min-w-0">
                <p class="font-medium text-gray-700 truncate">${complianceSelectedFile.name}</p>
                <p class="text-xs text-gray-400">${formatComplianceBytes(complianceSelectedFile.size)}</p>
            </div>
        </div>
        <button
            type="button"
            onclick="removeComplianceFile()"
            class="shrink-0 text-gray-400 hover:text-red-500 transition-colors cursor-pointer"
            title="Remove file">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    fileList.appendChild(li);
}

// ============================================================
//  AJAX SUBMISSION LOGIC
// ============================================================
function submitComplianceForm() {
    const form = document.getElementById('uploadComplianceForm');
    
    // TINANGGAL NA NATIN YUNG VALIDATION PARA SA DESCRIPTION DITO!

    // Validate File (Ito lang ang kailangang required)
    if (!complianceSelectedFile) {
        if(typeof showEndorseToast === 'function') showEndorseToast('Please upload a compliance file.', 'error');
        else alert('Please upload a compliance file.');
        return;
    }

    const formData = new FormData(form);
    formData.delete('compliance_file');
    formData.append('compliance_file', complianceSelectedFile);

    const btn = document.getElementById('confirmUploadComplianceBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Saving...`;

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        closeModal('upload-compliance-modal');
        if (data.status === 'success') {
            if(typeof showEndorseToast === 'function') showEndorseToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            if(typeof showEndorseToast === 'function') showEndorseToast(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = 'Submit Compliance';
        }
    })
    .catch((err) => {
        console.error(err);
        closeModal('upload-compliance-modal');
        if(typeof showEndorseToast === 'function') showEndorseToast('Network error. Please try again.', 'error');
    });
}
</script>