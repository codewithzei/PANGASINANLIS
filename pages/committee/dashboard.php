<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../components/header.php'; 
?>
<div class="flex min-h-screen relative bg-[#E2F0FF]">

    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen flex flex-col transition-all duration-300 w-full">
        
        <header class="bg-white border-b border-slate-200 sticky top-0 z-10 h-16 px-4 sm:px-6 flex justify-between items-center shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle" class="p-1.5 rounded-lg text-gray-500 hover:text-[#0033A1] hover:bg-[#E2F0FF] transition-all cursor-pointer focus:outline-none" title="Toggle Sidebar"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg></button>
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Super Admin Dashboard</h1>
            </div>

            <div class="flex items-center space-x-5">
                <div class="relative cursor-pointer group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300 group-hover:scale-110 group-hover:text-[#0033A1] text-gray-500" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                    <span class="absolute -top-0.5 -right-0.5 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="h-9 w-9 rounded-full bg-gray-200 overflow-hidden border border-gray-300 shrink-0">
                            <?php if (!empty($_SESSION['profile_picture'])): ?>
                                <img src="../../<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="h-full w-full object-cover">
                            <?php else: ?>
                                <div class="h-full w-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    <?php 
                                    $fName = $_SESSION['first_name'] ?? 'S';
                                    $lName = $_SESSION['last_name'] ?? 'A';
                                    echo strtoupper(substr($fName, 0, 1) . substr($lName, 0, 1)); 
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            
            <!-- Welcome Banner -->
            <div class="bg-linear-to-r from-[#0033A1] to-[#0047e0] rounded-2xl p-6 sm:p-8 text-white shadow-md mb-6 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute right-20 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative z-10">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Administrator'); ?>! 👋</h2>
                    <p class="text-blue-100 max-w-2xl text-sm sm:text-base leading-relaxed">Here is what's happening with the Legislative Information System today. Manage your network, track overall document flow, and oversee critical system configurations from your command center.</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center text-[#0033A1] shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Users</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">124</h3>
                    </div>
                </div>
                
                <!-- Stat 2 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Active Documents</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">45</h3>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pending Approvals</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">12</h3>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Recent Sessions</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">89</h3>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="user_accounts.php" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                        <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Add New User</span>
                    </a>
                    
                    <a href="document_types.php" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                        <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" x2="12" y1="18" y2="12"/><line x1="9" x2="15" y1="15" y2="15"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">New Doc Type</span>
                    </a>
                </div>
            </div>
            
        </main>
    </div>
</div>

<script src="/PangasinanLIS/src/js/global.js"></script>
</body>
</html>
