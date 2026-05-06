<?php
require_once __DIR__ . '/../../../includes/fetch_roles_data.php';
$roles = getAllRoles();
?>
<!-- Add User Modal Wrapper -->
<div id="user-add-modal" class="hidden">
    
    <!-- Modal Overlay -->
    <div 
        id="modalOverlay" 
        onclick="closeModal('user-add-modal')"
        class="fixed inset-0 bg-black/75 z-[101] transition-opacity duration-300 cursor-pointer">
    </div>

    <!-- Modal -->
    <div 
        id="modal" 
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[101] w-[calc(100%-2rem)] md:w-full max-w-2xl bg-white rounded-lg shadow-xl transition-all duration-300 max-h-[90vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200 shrink-0">
            <h2 class="text-xl font-semibold text-gray-800">Add User</h2>
            <button 
                id="closeBtn"
                onclick="closeModal('user-add-modal')"
                class="text-gray-400 hover:text-gray-600 transition duration-200 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Success/Error Message -->
        <div id="userMessage" class="hidden mx-6 mt-4 p-4 rounded-lg text-sm font-medium shrink-0">
            <div id="userMessageContent"></div>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="overflow-y-auto p-6 flex-1">
            <form id="userForm" method="POST" action="../../includes/actions_super_admin/add_user.php" enctype="multipart/form-data">
                
                <!-- Profile Picture Section -->
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Profile Picture</h3>
                <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0 mb-6">
                    <div class="shrink-0">
                        <div id="profilePicturePreview" class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 text-center sm:text-left w-full">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex flex-col sm:flex-row items-center gap-3 justify-center sm:justify-start">
                            <label class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200 w-full sm:w-auto text-center">
                                Choose File
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden" onchange="previewProfilePicture(this)">
                            </label>
                            <button type="button" onclick="removeProfilePicture()" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 transition duration-200 w-full sm:w-auto text-center border border-red-200 sm:border-transparent rounded-lg sm:rounded-none">
                                Remove
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Accepted formats: JPG, JPEG, PNG, GIF. Max size: 2MB</p>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" id="username" name="username" placeholder="Enter username" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" placeholder="Enter email" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required minlength="8">
                    </div>
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="role_id" name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                            <option value="" disabled selected>Select a role</option>
                            <?php if (!isset($roles['error'])): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['user_role_id']) ?>"><?= htmlspecialchars($role['user_role_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Profile Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200" required>
                    </div>
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Enter middle name (optional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                        <input type="text" id="suffix" name="suffix" placeholder="e.g., Jr., Sr., III (optional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                    <div class="md:col-span-2">
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" placeholder="Enter contact number (optional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-[#0033A1] focus:border-transparent transition duration-200">
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 shrink-0 bg-gray-50 rounded-b-lg">
            <button 
                type="button"
                id="cancelBtn"
                onclick="closeModal('user-add-modal')"
                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 cursor-pointer">
                Cancel
            </button>
            <button 
                type="submit"
                form="userForm"
                id="addBtn"
                class="px-5 py-2.5 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                Add User
            </button>
        </div>
    </div>
</div>

<script>
// Profile picture preview function
function previewProfilePicture(input) {
    const preview = document.getElementById('profilePicturePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Clear the preview div and add the image
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(input.files[0]);
        
        // Validate file size (2MB max)
        const file = input.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('File size exceeds 2MB. Please choose a smaller file.');
            input.value = ''; // Clear the file input
            removeProfilePicture();
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Please upload JPG, JPEG, PNG, or GIF images only.');
            input.value = ''; // Clear the file input
            removeProfilePicture();
        }
    }
}

// Remove profile picture function
function removeProfilePicture() {
    const preview = document.getElementById('profilePicturePreview');
    const fileInput = document.getElementById('profile_picture');
    
    // Reset the file input
    fileInput.value = '';
    
    // Reset preview to default icon
    preview.innerHTML = `
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
    `;
}

// Reset form when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    const modalId = 'user-add-modal';
    
    // Override openModal function to reset form if it exists
    if (window.openModal) {
        const originalOpenModal = window.openModal;
        window.openModal = function(id) {
            if (id === modalId) {
                // Reset the form
                const userForm = document.getElementById('userForm');
                if (userForm) {
                    userForm.reset();
                }
                
                // Reset profile picture preview
                removeProfilePicture();
                
                // Hide message if any
                const userMessage = document.getElementById('userMessage');
                if (userMessage) {
                    userMessage.classList.add('hidden');
                }
            }
            // Call original function
            originalOpenModal.call(this, id);
        };
    }
});
</script>
