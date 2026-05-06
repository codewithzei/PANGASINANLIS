<?php
$pageTitle = $pageTitle ?? ($_SESSION['role_name'] ?? 'Dashboard');
?>
<header
    class="bg-white border-b border-slate-200 sticky top-0 z-10 h-16 px-4 sm:px-6 flex justify-between items-center shrink-0 shadow-sm">
    <div class="flex items-center gap-4">
        <button id="sidebarToggle"
            class="p-1.5 rounded-lg text-gray-500 hover:text-[#0033A1] hover:bg-[#E2F0FF] transition-all cursor-pointer focus:outline-none"
            title="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>
    </div>

    <div class="flex items-center space-x-5">
        <div class="relative cursor-pointer group">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 group-hover:text-[#0033A1] text-gray-500"
                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                <path
                    d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
            </svg>
            <span class="absolute -top-0.5 -right-0.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="h-9 w-9 rounded-full bg-gray-200 overflow-hidden border border-gray-300 shrink-0">
                    <?php if (!empty($_SESSION['profile_picture'])): ?>
                        <?php
                        // Make sure profile picture path is absolute to home directory root
                        $pp_path = $_SESSION['profile_picture'];
                        if (strpos($pp_path, '/') !== 0 && strpos($pp_path, 'http') !== 0) {
                            $pp_path = '/PangasinanLIS/' . ltrim($pp_path, '/');
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($pp_path); ?>" alt="Profile"
                            class="h-full w-full object-cover">
                    <?php else: ?>
                        <div
                            class="h-full w-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                            <?php
                            $fName = $_SESSION['first_name'] ?? '';
                            $lName = $_SESSION['last_name'] ?? '';
                            $initials = strtoupper(substr($fName, 0, 1) . substr($lName, 0, 1));
                            echo $initials ? $initials : 'U';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-green-500 ring-2 ring-white"></span>
            </div>
            <div class="hidden sm:block">
                <h1 class="text-sm font-bold text-[#374151] leading-none">
                    <?php echo htmlspecialchars(trim(($_SESSION['first_name'] ?? 'User'))); ?>
                </h1>
                <p class="text-xs text-[#374151] mt-1 font-regular">
                    <?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Admin'); ?>
                </p>
            </div>
        </div>
    </div>
</header>