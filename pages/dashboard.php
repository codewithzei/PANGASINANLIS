<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: /PangasinanLIS/pages/auth/login");
    exit;
}

$role_name = $_SESSION['role_name'] ?? '';

include '../components/header.php'; 
?>
<div class="flex min-h-screen relative bg-[#E2F0FF]">

    <?php include '../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen flex flex-col transition-all duration-300 w-full">
        
        <?php 
        $pageTitle = ($role_name === 'Super Admin') ? 'Super Admin Dashboard' : 
                     (($role_name === 'Admin') ? 'Admin Overview' : 'Dashboard');
        include '../components/topbar.php'; 
        ?>

        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            <?php if ($role_name === 'Super Admin'): ?>
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-[#0033A1] to-[#0047e0] rounded-2xl p-6 sm:p-8 text-white shadow-md mb-6 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute right-20 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10">
                        <h2 class="text-2xl sm:text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Administrator'); ?>! 👋</h2>
                        <p class="text-blue-100 max-w-2xl text-sm sm:text-base leading-relaxed">Here is what's happening with the Legislative Information System today. Manage your network, track overall document flow, and oversee critical system configurations from your command center.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center text-[#0033A1] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Users</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">124</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Active Documents</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">45</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pending Approvals</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">12</h3>
                        </div>
                    </div>

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
                        <a href="/PangasinanLIS/pages/super_admin/user_accounts" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                            <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                            </div>
                            <span class="text-sm font-medium text-slate-700">Add New User</span>
                        </a>
                        
                        <a href="/PangasinanLIS/pages/super_admin/document_types" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                            <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" x2="12" y1="18" y2="12"/><line x1="9" x2="15" y1="15" y2="15"/></svg>
                            </div>
                            <span class="text-sm font-medium text-slate-700">New Doc Type</span>
                        </a>
                    </div>
                </div>

            <?php elseif ($role_name === 'Admin'): ?>
                <!-- ADMIN DASHBOARD CONTENT -->
                <div class="bg-gradient-to-r from-[#0033A1] to-[#0047e0] rounded-2xl p-6 sm:p-8 text-white shadow-md mb-6 relative overflow-hidden">
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10">
                        <h2 class="text-2xl sm:text-3xl font-bold mb-2">Welcome back to the Admin Dashboard, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Admin'); ?>!</h2>
                        <p class="text-blue-100 max-w-2xl text-sm sm:text-base leading-relaxed">Let's continue facilitating the workflow of documents across regions. View drafted documents, track routed records, and manage external offices from your overview.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10.4 12.6a2 2 0 1 1 3 3L8 21l-4 1 1-4Z"/><path d="M18 10.4a2 2 0 0 0-3-3"/><path d="M14 2v4.4"/><path d="M14.6 14.6 21 8.2"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Drafted Docs</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">18</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V4a2 2 0 0 1 2-2h8.5L20 7.5V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1"/><path d="M14 2v6h6"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Routed Out</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">42</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5zM6 12v5c3 3 9 3 12 0v-5"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">External Offices</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">15</h3>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 7v4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M14 9h-4"/><path d="M18 11h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h2"/><path d="M18 21V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Hospitals Linked</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">7</h3>
                        </div>
                    </div>
                </div>

                <!-- Operational Activity -->
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Operations</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="/PangasinanLIS/pages/admin/external_offices" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                            <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12h4"/><path d="M10 8h4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/></svg>
                            </div>
                            <span class="text-sm font-medium text-slate-700">Manage Offices</span>
                        </a>
                        
                        <a href="/PangasinanLIS/pages/admin/routing_options" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                            <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                            </div>
                            <span class="text-sm font-medium text-slate-700">Routing Directives</span>
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- DEFAULT DASHBOARD -->
                <div class="bg-gradient-to-r from-[#0033A1] to-[#0047e0] rounded-2xl p-6 sm:p-8 text-white shadow-md mb-6 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute right-20 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative z-10">
                        <h2 class="text-2xl sm:text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?>! 👋</h2>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="/PangasinanLIS/src/js/global.js"></script>
</body>
</html>
