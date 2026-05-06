<!-- Edit User Modal Wrapper -->
<div id="user-edit-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="editModalOverlay" 
        onclick="closeModal('user-edit-modal')"
        class="fixed inset-0 bg-black/75 z-101 transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="editModal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-101 w-[calc(100%-2rem)] md:w-full max-w-2xl bg-white rounded-lg shadow-xl transition-all duration-300 max-h-[90vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0">
            <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
            <button 
                id="editCloseBtn"
                onclick="closeModal('user-edit-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="editUserMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium shrink-0">
            <div id="editUserMessageContent"></div>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="overflow-y-auto p-6 flex-1">
            <form id="editUserForm" method="POST" action="../../includes/actions_super_admin/edit_user.php" enctype="multipart/form-data">
                
                <input type="hidden" id="edit_user_id" name="user_id">

                <!-- Profile Picture Section -->
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Profile Picture</h3>
                <div class="flex flex-col sm:flex-row items-start sm:space-x-5 space-y-4 sm:space-y-0 mb-6">
                    <!-- Avatar Preview -->
                    <div class="shrink-0 flex justify-center w-full sm:w-auto">
                        <div id="editProfilePicturePreview" class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border-2 border-gray-300">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Drag & Drop Zone -->
                    <div class="flex-1 w-full">
                        <label for="edit_profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Update Profile Picture</label>
                        <div
                            id="editDropZone"
                            onclick="document.getElementById('edit_profile_picture').click()"
                            class="w-full flex flex-col items-center justify-center gap-2 px-4 py-6 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-400 cursor-pointer transition-all duration-200 group"
                        >
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <p class="text-sm text-gray-600 group-hover:text-blue-600 transition-colors duration-200 font-medium">
                                Drag & drop your photo here, or <span class="text-blue-600 underline">browse</span>
                            </p>
                            <p id="editSelectedFileName" class="text-xs text-gray-400">No file selected &mdash; JPG, JPEG, PNG, GIF up to 2MB</p>
                            <input type="file" id="edit_profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden" onchange="previewEditProfilePicture(this)">
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-gray-400">Leave blank to keep the existing photo.</p>
                            <button type="button" onclick="removeEditProfilePicture()" class="text-xs text-red-500 hover:text-red-700 font-medium transition duration-200">
                                Remove Selected
                            </button>
                        </div>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Account Status & Role</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username (Read Only)</label>
                        <input type="text" id="edit_username" class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg outline-none text-gray-600 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email (Read Only)</label>
                        <input type="email" id="edit_email" class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg outline-none text-gray-600 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="edit_role_id" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="edit_role_id" name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                            <option value="" disabled>Select a role</option>
                            <?php if (!isset($roles['error'])): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['user_role_id']) ?>"><?= htmlspecialchars($role['user_role_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_account_status" class="block text-sm font-medium text-gray-700 mb-1">Account Status *</label>
                        <select id="edit_account_status" name="account_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                            <option value="Active">Active</option>
                            <option value="Blocked">Blocked</option>
                            <option value="Deactivated">Deactivated</option>
                        </select>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Profile Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <div>
                        <label for="edit_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="edit_first_name" name="first_name" placeholder="Enter first name" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="edit_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="edit_last_name" name="last_name" placeholder="Enter last name" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="edit_middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" id="edit_middle_name" name="middle_name" placeholder="Enter middle name" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                    <div>
                        <label for="edit_suffix" class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                        <input type="text" id="edit_suffix" name="suffix" placeholder="e.g., Jr., Sr., III" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" id="edit_contact_number" name="contact_number" placeholder="Enter contact number" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button 
                type="button"
                id="editCancelBtn"
                onclick="closeModal('user-edit-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="editUserForm"
                id="editSubmitBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
// Profile picture preview function for edit
function previewEditProfilePicture(input) {
    const preview = document.getElementById('editProfilePicturePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
        
        const file = input.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('File size exceeds 2MB. Please choose a smaller file.');
            input.value = '';
            removeEditProfilePicture(true);
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Please upload JPG, JPEG, PNG, or GIF images only.');
            input.value = '';
            removeEditProfilePicture(true);
        }
    }
}

// Remove profile picture function for edit (visually only, server keeps old unless replaced or explicitly told to delete)
function removeEditProfilePicture(keepExisting = false) {
    const preview = document.getElementById('editProfilePicturePreview');
    const fileInput = document.getElementById('edit_profile_picture');
    fileInput.value = '';
    
    // During an actual clearing, it only clears the local file input. 
    // To truly delete from DB, a different mechanism would be needed.
    if(!keepExisting && window.currentEditProfilePictureUrl) {
        // revert to existing picture if they just canceled a new upload
        let pp_url = window.currentEditProfilePictureUrl;
        if (!pp_url.startsWith('/') && !pp_url.startsWith('http')) {
            pp_url = '/PangasinanLIS/' + pp_url;
        }
        preview.innerHTML = `<img src="${pp_url}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='/PangasinanLIS/assets/Province_of_Pangasinan.png';">`;
    } else {
        preview.innerHTML = `
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        `;
    }
}
</script>
