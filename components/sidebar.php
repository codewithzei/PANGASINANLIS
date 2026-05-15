<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$roleName = $_SESSION['role_name'] ?? '';

// I-set natin sa zero by default
$inboxCount = 0;
$receivedCount = 0;

// Siguraduhing naka-connect ang DB at may naka-login
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    try {
        // 1. BILANGIN ANG RECEIVED DOCUMENTS (Hawak mo na)
        $stmtRcv = $pdo->prepare("
            SELECT COUNT(d.document_id)
            FROM documents d
            WHERE d.current_owner_user_id = :user_id
        ");
        $stmtRcv->execute([':user_id' => $userId]);
        $receivedCount = $stmtRcv->fetchColumn();

        // 2. BILANGIN ANG INBOX (Papasok pa lang sa inyo, wala pang owner)
        // SP Secretary = routing_option_id 1
        $myOfficeId = 1;

        $stmtInbox = $pdo->prepare("
            SELECT COUNT(document_id)
            FROM documents
            WHERE current_owner_user_id IS NULL
            AND current_routing_option_id = :office_id
        ");
        $stmtInbox->execute([':office_id' => $myOfficeId]);
        $inboxCount = $stmtInbox->fetchColumn();

    } catch (PDOException $e) {
        // Hayaan lang kung may error para hindi masira ang sidebar
    }
}
?>

<style>
    /* Sidebar collapse transition */
    #sidebar {
        transition: width 0.3s ease, transform 0.3s ease;
        overflow-x: hidden;
    }

    /* Force width collapse overriding Tailwind */
    #sidebar.sidebar-collapsed {
        width: 4rem !important;
    }

    /* Force main content adjust */
    .main-collapsed {
        margin-left: 4rem !important;
    }
    
    /* Native Mobile Hide Logic to bypass Tailwind compiler dependency */
    @media (max-width: 767px) {
        #sidebar {
            transform: translateX(-100%);
        }
        #sidebar.mobile-show {
            transform: translateX(0);
        }
    }
    
    @media (min-width: 768px) {
        #sidebar {
            transform: translateX(0);
        }
        .md\:ml-64 {
            transition: margin-left 0.3s ease;
        }
    }

    /* When collapsed, hide horizontal overflow but allow vertical scroll for open submenus */
    #sidebar.sidebar-collapsed nav {
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* Smooth label/text transitions */
    .nav-label,
    .sidebar-logo-text,
    .sidebar-profile-text,
    .dropdown-arrow {
        opacity: 1;
        max-width: 200px;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.2s ease, max-width 0.3s ease;
    }

    /* Hide labels when collapsed */
    #sidebar.sidebar-collapsed .nav-label,
    #sidebar.sidebar-collapsed .sidebar-logo-text,
    #sidebar.sidebar-collapsed .sidebar-profile-text,
    #sidebar.sidebar-collapsed .dropdown-arrow {
        opacity: 0;
        max-width: 0;
        pointer-events: none;
    }

    /* Nav section: normal state */
    .nav-section {
        padding: 0 0.5rem;
        transition: all 0.2s ease;
    }

    /* Nav section: show · · · when collapsed */
    #sidebar.sidebar-collapsed .nav-section {
        color: transparent;
        position: relative;
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 0;
        height: 24px;
        margin-top: 12px;
        margin-bottom: 4px;
    }

    #sidebar.sidebar-collapsed .nav-section::after {
        content: '\2026';
        /* ellipsis */
        position: absolute;
        color: #cbd5e1;
        font-size: 14px;
        letter-spacing: 1px;
        font-weight: normal;
    }

    /* Center icons when collapsed */
    #sidebar.sidebar-collapsed nav a,
    #sidebar.sidebar-collapsed nav button {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #sidebar.sidebar-collapsed nav a .mr-2,
    #sidebar.sidebar-collapsed nav button .mr-2 {
        margin-right: 0 !important;
    }

    /* Collapsed submenu: icon-only rows, perfectly centered in the 4rem rail */
    #sidebar.sidebar-collapsed #dataManagementBtn {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #sidebar.sidebar-collapsed #dataManagementBtn .nav-label,
    #sidebar.sidebar-collapsed #dataManagementBtn .dropdown-arrow {
        display: none !important;
    }

    #sidebar.sidebar-collapsed #dataManagementMenu {
        background: transparent !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #sidebar.sidebar-collapsed #dataManagementMenu a {
        justify-content: center !important;
        padding: 6px 0 !important;
        margin: 0 !important;
        width: 100% !important;
        border-radius: 4px !important;
        min-height: 36px !important;
        display: flex !important;
        align-items: center !important;
    }

    #sidebar.sidebar-collapsed #dataManagementMenu a .mr-2 {
        margin-right: 0 !important;
    }

    /* Collapsed profile section */
    #sidebar.sidebar-collapsed .sidebar-profile-avatar {
        margin: 0 auto;
    }

    #sidebar.sidebar-collapsed .sidebar-logout-btn {
        display: none;
    }

    /* JS-powered tooltip (fixed position so it won't be clipped) */
    .sidebar-tooltip-popup {
        position: fixed;
        background: #1e293b;
        color: #fff;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        white-space: nowrap;
        z-index: 9999;
        pointer-events: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
        opacity: 0;
        transition: opacity 0.12s ease;
    }

    .sidebar-tooltip-popup.visible {
        opacity: 1;
    }

    .sidebar-tooltip-popup::before {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-right-color: #1e293b;
    }

     /* Sidebar badge transition and collapsed state */
    .sidebar-badge {
        transition: all 0.2s ease;
    }

    #sidebar.sidebar-collapsed .sidebar-badge {
        position: absolute;
        /* Inangat natin at tinulak pakanan */
        top: 2px; 
        right: 4px; 
        /* Pwede rin gumamit ng transform para eksakto sa kanto: */
        /* transform: translate(30%, -30%); */
        padding: 0.15rem 0.3rem;
        font-size: 0.65rem;
        line-height: 1;
        border: 2px solid white;
        /* Optional: Siguraduhing bilog pa rin kahit lumiit */
        border-radius: 9999px; 
        z-index: 10;
    }
</style>

<aside id="sidebar"
    class="fixed inset-y-0 left-0 w-64 bg-white text-white flex flex-col shadow-xl z-40 transition-all duration-300">
    <!-- Header Logo -->
    <div class="p-3 flex items-center gap-2 shadow-[inset_0_-1px_0_0_#E5E7EB] shrink-0 overflow-hidden">
        <img src="/PangasinanLIS/assets/Province_of_Pangasinan.png" alt="Logo" class="h-10 w-auto shrink-0">
        <div class="sidebar-logo-text overflow-hidden">
            <h1 class="text-lg font-bold text-[#0033A1] leading-none whitespace-nowrap">Pangasinan</h1>
            <p class="text-xs text-[#374151] mt-1 font-semibold whitespace-nowrap">Legislative Information System</p>
        </div>
    </div>

    <!-- Navigation Scrollable Area -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto min-h-0 custom-scrollbar">

        <!-- Common HOME section -->
        <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mb-1">HOME</div>
        <a href="/PangasinanLIS/pages/dashboard" data-tooltip="Dashboard"
            class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
            <span class="mr-2 shrink-0">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z" />
                </svg>
            </span>
            <span class="nav-label">Dashboard</span>
        </a>

        <?php if ($roleName === 'Super Admin'): ?>
            <!-- SUPER ADMIN SIDEBAR -->
            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">ACCESS CONTROL</div>
            <a href="/PangasinanLIS/pages/super_admin/user_accounts" data-tooltip="User Accounts"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </span>
                <span class="nav-label">User Accounts</span>
            </a>
            <a href="/PangasinanLIS/pages/super_admin/user_roles" data-tooltip="User Roles"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        <path d="M6.376 18.91a6 6 0 0 1 11.249.003" />
                        <circle cx="12" cy="11" r="4" />
                    </svg>
                </span>
                <span class="nav-label">User Roles</span>
            </a>
            <a href="#" data-tooltip="Audit Logs"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                    </svg>
                </span>
                <span class="nav-label">Audit Logs</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">CONFIGURATION</div>
            <a href="/PangasinanLIS/pages/super_admin/document_types" data-tooltip="Document Types"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                </span>
                <span class="nav-label">Document Types</span>
            </a>
            <a href="/PangasinanLIS/pages/super_admin/muni_cities" data-tooltip="Municipalities"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 18v-7" />
                        <path
                            d="M11.12 2.198a2 2 0 0 1 1.76.006l7.866 3.847c.476.233.31.949-.22.949H3.474c-.53 0-.695-.716-.22-.949z" />
                        <path d="M14 18v-7" />
                        <path d="M18 18v-7" />
                        <path d="M3 22h18" />
                        <path d="M6 18v-7" />
                    </svg>
                </span>
                <span class="nav-label">Municipalities</span>
            </a>
            <a href="/PangasinanLIS/pages/super_admin/document_statuses" data-tooltip="Document Status"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M16 22h2a2 2 0 0 0 2-2V8a2.4 2.4 0 0 0-.706-1.706l-3.588-3.588A2.4 2.4 0 0 0 14 2H6a2 2 0 0 0-2 2v2.85" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="M8 14v2.2l1.6 1" />
                        <circle cx="8" cy="16" r="6" />
                    </svg>
                </span>
                <span class="nav-label">Document Status</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-semibold px-2 mt-6 mb-1">SETTINGS</div>
            <a href="#" data-tooltip="Account Settings"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m14.305 19.53.923-.382" />
                        <path d="m15.228 16.852-.923-.383" />
                        <path d="m16.852 15.228-.383-.923" />
                        <path d="m16.852 20.772-.383.924" />
                        <path d="m19.148 15.228.383-.923" />
                        <path d="m19.53 21.696-.382-.924" />
                        <path d="M2 21a8 8 0 0 1 10.434-7.62" />
                        <path d="m20.772 16.852.924-.383" />
                        <path d="m20.772 19.148.924.383" />
                        <circle cx="10" cy="8" r="5" />
                        <circle cx="18" cy="18" r="3" />
                    </svg>
                </span>
                <span class="nav-label">Account Settings</span>
            </a>

        <?php elseif ($roleName === 'Admin' || $roleName === 'System Admin'): ?>
            <!-- ADMIN SIDEBAR -->
            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">DOCUMENT MANAGEMENT</div>
            <a href="#" data-tooltip="Drafts"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M12.659 22H18a2 2 0 0 0 2-2V8a2.4 2.4 0 0 0-.706-1.706l-3.588-3.588A2.4 2.4 0 0 0 14 2H6a2 2 0 0 0-2 2v9.34" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path
                            d="M10.378 12.622a1 1 0 0 1 3 3.003L8.36 20.637a2 2 0 0 1-.854.506l-2.867.837a.5.5 0 0 1-.62-.62l.836-2.869a2 2 0 0 1 .506-.853z" />
                    </svg>
                </span>
                <span class="nav-label">Drafts</span>
            </a>
            <a href="/PangasinanLIS/pages/admin/routed_documents" data-tooltip="Routed Documents"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M4 11V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.706.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-1" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="M2 15h10" />
                        <path d="m9 18 3-3-3-3" />
                    </svg>
                </span>
                <span class="nav-label">Routed Documents</span>
            </a>
            <a href="#" data-tooltip="Resubmission Handling"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                        <path d="M21 3v5h-5" />
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                        <path d="M8 16H3v5" />
                    </svg>
                </span>
                <span class="nav-label">Resubmission Handling</span>
            </a>
            <a href="#" data-tooltip="Communications"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                        <path d="M12 11h.01" />
                        <path d="M16 11h.01" />
                        <path d="M8 11h.01" />
                    </svg>
                </span>
                <span class="nav-label">Communications</span>
            </a>
            <a href="#" data-tooltip="Filed Documents"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="m9 15 2 2 4-4" />
                    </svg>
                </span>
                <span class="nav-label">Filed Documents</span>
            </a>
            <a href="#" data-tooltip="Archived Documents"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2" />
                    </svg>
                </span>
                <span class="nav-label">Archived Documents</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">REPORTS & AUDIT</div>
            <a href="#" data-tooltip="Report Generation"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="M8 18v-1" />
                        <path d="M12 18v-6" />
                        <path d="M16 18v-3" />
                    </svg>
                </span>
                <span class="nav-label">Report Generation</span>
            </a>
            <a href="#" data-tooltip="Logs & History"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                    </svg>
                </span>
                <span class="nav-label">Logs & History</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">SYSTEM ARCHIVE</div>
            <a href="#" data-tooltip="Archive"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="5" x="2" y="3" rx="1" />
                        <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                        <path d="M10 12h4" />
                    </svg>
                </span>
                <span class="nav-label">Archive</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">SYSTEM SETTINGS</div>
            <a href="#" data-tooltip="Account Settings"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 20a6 6 0 0 0-12 0" />
                        <circle cx="12" cy="10" r="4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </span>
                <span class="nav-label">Account Settings</span>
            </a>
            <a href="#" data-tooltip="User Management"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m14.305 19.53.923-.382" />
                        <path d="m15.228 16.852-.923-.383" />
                        <path d="m16.852 15.228-.383-.923" />
                        <path d="m16.852 20.772-.383.924" />
                        <path d="m19.148 15.228.383-.923" />
                        <path d="m19.53 21.696-.382-.924" />
                        <path d="M2 21a8 8 0 0 1 10.434-7.62" />
                        <path d="m20.772 16.852.924-.383" />
                        <path d="m20.772 19.148.924.383" />
                        <circle cx="10" cy="8" r="5" />
                        <circle cx="18" cy="18" r="3" />
                    </svg>
                </span>
                <span class="nav-label">User Management</span>
            </a>

            <div class="relative group/data">
                <button id="dataManagementBtn" data-tooltip="Data Management"
                    class="w-full flex items-center px-2 py-2 mt-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all focus:outline-none">
                    <span class="mr-2 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M3 5V19A9 3 0 0 0 21 19V5" />
                            <path d="M3 12A9 3 0 0 0 21 12" />
                        </svg>
                    </span>
                    <span class="nav-label flex-1 text-left">Data Management</span>
                    <svg id="arrowIcon" class="dropdown-arrow w-4 h-4 ml-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Accordion submenu (expanded sidebar) -->
                <div id="dataManagementMenu"
                    class="max-h-0 overflow-hidden bg-gray-50 border-[#E2F0FF] space-y-1 transition-all duration-300 ease-in-out">
                    <a href="/PangasinanLIS/pages/admin/external_offices"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 12h4" />
                                <path d="M10 8h4" />
                                <path d="M14 21v-3a2 2 0 0 0-4 0v3" />
                                <path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2" />
                                <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                            </svg>
                        </span>
                        <span class="nav-label">External Offices</span>
                    </a>
                    <a href="/PangasinanLIS/pages/admin/hospitals"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v4" />
                                <path d="M14 21v-3a2 2 0 0 0-4 0v3" />
                                <path d="M14 9h-4" />
                                <path d="M18 11h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h2" />
                                <path d="M18 21V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16" />
                            </svg>
                        </span>
                        <span class="nav-label">Hospitals</span>
                    </a>
                    <a href="/PangasinanLIS/pages/admin/source_types"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 9V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1" />
                                <path d="M2 13h10" />
                                <path d="m9 16 3-3-3-3" />
                            </svg>
                        </span>
                        <span class="nav-label">Source Types</span>
                    </a>
                    <a href="/PangasinanLIS/pages/admin/routing_options"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
                                <path d="m21.854 2.147-10.94 10.939" />
                            </svg>
                        </span>
                        <span class="nav-label">Routing Options</span>
                    </a>
                    <a href="/PangasinanLIS/pages/admin/communication_categories"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                <path d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1" />
                            </svg>
                        </span>
                        <span class="nav-label">Communication Categories</span>
                    </a>
                    <a href="/PangasinanLIS/pages/admin/checklists" data-tooltip="Checklists"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M13 5h8" />
                                <path d="M13 12h8" />
                                <path d="M13 19h8" />
                                <path d="m3 17 2 2 4-4" />
                                <rect x="3" y="4" width="6" height="6" rx="1" />
                            </svg>
                        </span>
                        <span class="nav-label">Checklists</span>
                    </a>
                </div>
            </div>


            <script>
                (function () {
                    const btn   = document.getElementById('dataManagementBtn');
                    const menu  = document.getElementById('dataManagementMenu');
                    const arrow = document.getElementById('arrowIcon');

                    if (!btn) return;

                    btn.addEventListener('click', function () {
                        if (menu.style.maxHeight && menu.style.maxHeight !== '0px') {
                            menu.style.maxHeight = '0px';
                            arrow.style.transform = 'rotate(0deg)';
                        } else {
                            menu.style.maxHeight = menu.scrollHeight + 'px';
                            arrow.style.transform = 'rotate(180deg)';
                        }
                    });
                })();
            </script>

            <?php elseif ($roleName === 'SP Secretary' || $roleName === 'SP Secretary'): ?>
            <!-- ADMIN SIDEBAR -->
            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">DOCUMENT MANAGEMENT</div>
            <a href="/PangasinanLIS/pages/sp_secretary/inbox" data-tooltip="Inbox" data-badge="inbox"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-inbox-icon lucide-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                </span>
                <span class="nav-label flex-1">Inbox</span>
                
                <!-- INBOX BADGE COUNTER -->
                <?php if (isset($inboxCount) && $inboxCount > 0): ?>
                    <span class="sidebar-badge inbox-count-badge bg-[#0033A1] text-white text-xs font-semibold w-5 h-5 flex items-center justify-center rounded-full ml-auto shadow-sm shrink-0">
                        <?= $inboxCount > 99 ? '99+' : $inboxCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- RECEIVED DOCUMENTS LINK WITH BADGE -->
            <a href="/PangasinanLIS/pages/sp_secretary/received_documents" data-tooltip="Received Documents" data-badge="received"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check-icon lucide-file-check"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="m9 15 2 2 4-4"/></svg>
                </span>
                <span class="nav-label flex-1">Received Documents</span>
                
                <!-- RECEIVED BADGE COUNTER -->
                <?php if (isset($receivedCount) && $receivedCount > 0): ?>
                    <span class="sidebar-badge received-count-badge bg-[#0033A1] text-white text-xs font-semibold w-5 h-5 flex items-center justify-center rounded-full ml-auto shadow-sm shrink-0">
                        <?= $receivedCount > 99 ? '99+' : $receivedCount ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="/PangasinanLIS/pages/sp_secretary/routed_documents" data-tooltip="Routed Documents"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M4 11V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.706.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-1" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="M2 15h10" />
                        <path d="m9 18 3-3-3-3" />
                    </svg>
                </span>
                <span class="nav-label">Routed Documents</span>
            </a>
            <a href="#" data-tooltip="Communications"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                        <path d="M12 11h.01" />
                        <path d="M16 11h.01" />
                        <path d="M8 11h.01" />
                    </svg>
                </span>
                <span class="nav-label">Communications</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">REPORTS & AUDIT</div>
            <a href="#" data-tooltip="Report Generation"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                        <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                        <path d="M8 18v-1" />
                        <path d="M12 18v-6" />
                        <path d="M16 18v-3" />
                    </svg>
                </span>
                <span class="nav-label">Report Generation</span>
            </a>
            <a href="#" data-tooltip="Logs & History"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                    </svg>
                </span>
                <span class="nav-label">Logs & History</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">SYSTEM SETTINGS</div>
            <a href="#" data-tooltip="Account Settings"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 20a6 6 0 0 0-12 0" />
                        <circle cx="12" cy="10" r="4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </span>
                <span class="nav-label">Account Settings</span>
            </a>

            <?php elseif ($roleName === 'Committee' || $roleName === 'Committee'): ?>
            <!-- ADMIN SIDEBAR -->
            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">DOCUMENT MANAGEMENT</div>
            <a href="/PangasinanLIS/pages/committee/inbox" data-tooltip="Inbox"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-inbox-icon lucide-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                </span>
                <span class="nav-label flex-1">Inbox</span>
            </a>

            <a href="/PangasinanLIS/pages/committee/referred_documents" data-tooltip="Referred Documents"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                </span>
                <span class="nav-label flex-1">Referred Documents</span>
            </a>

            <a href="/PangasinanLIS/pages/committee/committee_hearings" data-tooltip="Committee Hearings"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-icon lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>
                </span>
                <span class="nav-label flex-1">Committee Hearings</span>
            </a>

            <a href="/PangasinanLIS/pages/committee/all_reports" data-tooltip="All Reports"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-files-icon lucide-files"><path d="M15 2h-4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V8"/><path d="M16.706 2.706A2.4 2.4 0 0 0 15 2v5a1 1 0 0 0 1 1h5a2.4 2.4 0 0 0-.706-1.706z"/><path d="M5 7a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h8a2 2 0 0 0 1.732-1"/></svg>
                </span>
                <span class="nav-label flex-1">All Reports</span>
            </a>

            <a href="/PangasinanLIS/pages/committee/administrative_cases" data-tooltip="Administrative Cases"
                class="relative flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scale-icon lucide-scale"><path d="M12 3v18"/><path d="m19 8 3 8a5 5 0 0 1-6 0zV7"/><path d="M3 7h1a17 17 0 0 0 8-2 17 17 0 0 0 8 2h1"/><path d="m5 8 3 8a5 5 0 0 1-6 0zV7"/><path d="M7 21h10"/></svg>
                </span>
                <span class="nav-label flex-1">Administrative Cases</span>
            </a>

            <a href="#" data-tooltip="Communications"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                        <path d="M12 11h.01" />
                        <path d="M16 11h.01" />
                        <path d="M8 11h.01" />
                    </svg>
                </span>
                <span class="nav-label">Communications</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">AUDIT</div>
            <a href="#" data-tooltip="Logs & History"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                    </svg>
                </span>
                <span class="nav-label">Logs & History</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">SYSTEM ARCHIVE</div>
            <a href="#" data-tooltip="Archive"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="5" x="2" y="3" rx="1" />
                        <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                        <path d="M10 12h4" />
                    </svg>
                </span>
                <span class="nav-label">Archive</span>
            </a>

            <div class="nav-section text-[#0033A1] text-xs font-bold px-2 mt-6 mb-1">SYSTEM SETTINGS</div>
            <a href="#" data-tooltip="Account Settings"
                class="flex items-center px-2 py-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all group">
                <span class="mr-2 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 20a6 6 0 0 0-12 0" />
                        <circle cx="12" cy="10" r="4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                </span>
                <span class="nav-label">Account Settings</span>
            </a>

            <div class="relative group/data">
                <button id="dataManagementBtn" data-tooltip="Data Management"
                    class="w-full flex items-center px-2 py-2 mt-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-sm font-semibold rounded-sm transition-all focus:outline-none">
                    <span class="mr-2 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3" />
                            <path d="M3 5V19A9 3 0 0 0 21 19V5" />
                            <path d="M3 12A9 3 0 0 0 21 12" />
                        </svg>
                    </span>
                    <span class="nav-label flex-1 text-left">Data Management</span>
                    <svg id="arrowIcon" class="dropdown-arrow w-4 h-4 ml-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Accordion submenu (expanded sidebar) -->
                <div id="dataManagementMenu"
                    class="max-h-0 overflow-hidden bg-gray-50 border-[#E2F0FF] space-y-1 transition-all duration-300 ease-in-out">
                    <a href="/PangasinanLIS/pages/committee/opinion_offices" data-tooltip="Opinion Offices"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 12h4" />
                                <path d="M10 8h4" />
                                <path d="M14 21v-3a2 2 0 0 0-4 0v3" />
                                <path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2" />
                                <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" />
                            </svg>
                        </span>
                        <span class="nav-label">Opinion Offices</span>
                    </a>

                    <a href="/PangasinanLIS/pages/committee/opinion_statuses" data-tooltip="Opinion Status"
                        class="flex items-center px-2 py-2 mt-2 ml-2 text-[#374151] hover:bg-[#E2F0FF] hover:text-[#0033A1] text-xs font-semibold rounded-sm transition-all group">
                        <span class="mr-2 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M16 22h2a2 2 0 0 0 2-2V8a2.4 2.4 0 0 0-.706-1.706l-3.588-3.588A2.4 2.4 0 0 0 14 2H6a2 2 0 0 0-2 2v2.85" />
                                <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                                <path d="M8 14v2.2l1.6 1" />
                                <circle cx="8" cy="16" r="6" />
                            </svg>
                        </span>
                        <span class="nav-label">Opinion Status</span>
                    </a>
                </div>
            </div>


            <script>
                (function () {
                    const btn   = document.getElementById('dataManagementBtn');
                    const menu  = document.getElementById('dataManagementMenu');
                    const arrow = document.getElementById('arrowIcon');

                    if (!btn) return;

                    btn.addEventListener('click', function () {
                        if (menu.style.maxHeight && menu.style.maxHeight !== '0px') {
                            menu.style.maxHeight = '0px';
                            arrow.style.transform = 'rotate(0deg)';
                        } else {
                            menu.style.maxHeight = menu.scrollHeight + 'px';
                            arrow.style.transform = 'rotate(180deg)';
                        }
                    });
                })();
            </script>
        <?php endif; ?>

    </nav>

    <!-- Bottom User Profile Section -->
    <div class="p-3 shadow-[inset_0_1px_0_0_#E5E7EB] flex items-center justify-between shrink-0 overflow-hidden">
        <div class="flex items-center gap-2 overflow-hidden">
            <div data-tooltip="<?php echo htmlspecialchars(trim(($_SESSION['first_name'] ?? 'System') . ' ' . ($_SESSION['last_name'] ?? 'User'))); ?>"
                class="sidebar-profile-avatar h-10 w-10 rounded-full bg-gray-200 flex flex-col items-center justify-center text-[#374151] overflow-hidden shrink-0 border border-gray-300">
                <?php if (!empty($_SESSION['profile_picture'])): ?>
                    <img src="/PangasinanLIS/<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile"
                        class="h-full w-full object-cover">
                <?php else: ?>
                    <div class="h-10 w-10 bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                        <?php
                        $fName = $_SESSION['first_name'] ?? 'U';
                        $lName = $_SESSION['last_name'] ?? 'ser';
                        echo strtoupper(substr($fName, 0, 1) . substr($lName, 0, 1));
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="sidebar-profile-text flex flex-col min-w-0">
                <span class="text-sm font-semibold text-[#374151] truncate">
                    <?php echo htmlspecialchars(trim(($_SESSION['first_name'] ?? 'System') . ' ' . ($_SESSION['last_name'] ?? 'User'))); ?>
                </span>
                <span
                    class="text-xs text-[#0033A1] font-semibold truncate"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Administrator'); ?></span>
            </div>
        </div>

        <a href="/PangasinanLIS/includes/auth_logout"
        class="sidebar-logout-btn text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors shrink-0"
            title="Logout">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>
    <script>
        (function () {
            let tip = null;
            let tipTimeout = null;

            function showTooltip(el) {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar || !sidebar.classList.contains('sidebar-collapsed')) return;

                const text = el.getAttribute('data-tooltip');
                if (!text) return;

                clearTimeout(tipTimeout);
                hideTooltip();

                tip = document.createElement('div');
                tip.className = 'sidebar-tooltip-popup';
                tip.textContent = text;
                document.body.appendChild(tip);

                const rect = el.getBoundingClientRect();
                const tipH = tip.offsetHeight;
                const tipW = tip.offsetWidth;

                // Position to the right of the element, vertically centered
                let top = rect.top + (rect.height - tipH) / 2;
                let left = rect.right + 10;

                // Keep within viewport vertically
                top = Math.max(8, Math.min(top, window.innerHeight - tipH - 8));

                tip.style.top = top + 'px';
                tip.style.left = left + 'px';

                // Trigger fade in
                requestAnimationFrame(() => {
                    if (tip) tip.classList.add('visible');
                });
            }

            function hideTooltip() {
                if (tip) {
                    tip.remove();
                    tip = null;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar) return;

                sidebar.querySelectorAll('[data-tooltip]').forEach(function (el) {
                    el.addEventListener('mouseenter', function () { showTooltip(el); });
                    el.addEventListener('mouseleave', hideTooltip);
                    el.addEventListener('click', hideTooltip);
                });
            });
        })();
    </script>
</aside>