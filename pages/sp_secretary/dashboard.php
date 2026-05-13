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
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Admin Overview</h1>
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
                                    $fName = $_SESSION['first_name'] ?? 'A';
                                    $lName = $_SESSION['last_name'] ?? 'D';
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
                <!-- Stat 1 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10.4 12.6a2 2 0 1 1 3 3L8 21l-4 1 1-4Z"/><path d="M18 10.4a2 2 0 0 0-3-3"/><path d="M14 2v4.4"/><path d="M14.6 14.6 21 8.2"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Drafted Docs</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">18</h3>
                    </div>
                </div>
                
                <!-- Stat 2 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V4a2 2 0 0 1 2-2h8.5L20 7.5V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1"/><path d="M14 2v6h6"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Routed Out</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">42</h3>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="h-12 w-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5zM6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">External Offices</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">15</h3>
                    </div>
                </div>

                <!-- Stat 4 -->
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
                    <a href="external_offices.php" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                        <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12h4"/><path d="M10 8h4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Manage Offices</span>
                    </a>
                    
                    <a href="routing_options.php" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-[#0033A1] hover:shadow-md transition-all text-center group">
                        <div class="h-10 w-10 mx-auto rounded-full bg-blue-50 flex items-center justify-center text-[#0033A1] group-hover:scale-110 transition-transform mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Routing Directives</span>
                    </a>
                </div>
            </div>
            
        </main>
    </div>
</div>

<script src="/PangasinanLIS/src/js/global.js"></script>
<script src="/PangasinanLIS/src/js/page_transition.js"></script>
</body>
</html>
