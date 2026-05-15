<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/badges.php';
require_once '../../includes/fetch_committee_reports_data.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /PangasinanLIS/pages/auth/login');
    exit;
}

$showReportCreatedBanner = isset($_GET['report_created']) && $_GET['report_created'] === '1';
$initialTab = isset($_GET['tab']) ? strtolower(trim((string) $_GET['tab'])) : 'all';
if (!in_array($initialTab, ['all', 'committee', 'joint'], true)) {
    $initialTab = 'all';
}

$allReportsRows = getCommitteeReportsDataForUser((int) $userId);
$reportsRenderError = null;
$reportsAllList = [];
$reportsCommitteeList = [];
$reportsJointList = [];

if (isset($allReportsRows['error'])) {
    $reportsRenderError = $allReportsRows['error'];
} else {
    $reportsAllList = $allReportsRows;
    foreach ($allReportsRows as $r) {
        $isJoint = stripos((string) ($r['report_type_name'] ?? ''), 'joint') !== false;
        if ($isJoint) {
            $reportsJointList[] = $r;
        } else {
            $reportsCommitteeList[] = $r;
        }
    }
}

/** @param array<int, array<string, mixed>> $rows */
function render_committee_report_table_rows(?string $errorMsg, array $rows): void
{
    $colspan = 11;
    $pad = 'px-6 sm:px-10 py-4 sm:py-5';
    $td = $pad . ' align-middle text-sm text-gray-700';
    $tdNum = $pad . ' align-middle text-sm text-gray-700 whitespace-nowrap';
    $tdType = $pad . ' align-middle text-sm text-gray-700 min-w-[12rem] whitespace-normal break-words';
    $tdTitle = $pad . ' align-middle text-base text-gray-700 font-normal min-w-[28rem] max-w-none whitespace-normal break-words';
    $tdSubject = $pad . ' align-middle text-base text-gray-700 font-normal min-w-[28rem] max-w-none whitespace-normal break-words';
    $tdCommittees = $pad . ' align-middle text-sm text-gray-700 font-normal min-w-[24rem] max-w-none whitespace-normal break-words';

    if ($errorMsg !== null) {
        echo '<tr><td colspan="' . $colspan . '" class="px-6 py-8 text-center text-red-600">' . htmlspecialchars($errorMsg) . '</td></tr>';
        return;
    }
    if (empty($rows)) {
        return;
    }
    $cell = static function ($v): string {
        if ($v === null || $v === '') {
            return '—';
        }
        return htmlspecialchars((string) $v);
    };
    foreach ($rows as $r) {
        $rid = (int) ($r['committee_report_id'] ?? 0);
        $did = (int) ($r['document_id'] ?? 0);
        $statusName = $r['status_name'] ?? 'N/A';
        $bgClass = function_exists('getStatusBadgeClass') ? getStatusBadgeClass($statusName) : 'bg-gray-100 text-gray-800 border-gray-200';

        echo '<tr class="hover:bg-slate-50 transition-colors duration-150">';
        echo '<td class="' . $tdNum . ' font-bold text-gray-900">' . $cell($r['committee_report_number'] ?? '') . '</td>';
        echo '<td class="' . $tdNum . ' font-bold text-gray-900">' . $cell($r['joint_committee_report_number'] ?? '') . '</td>';
        echo '<td class="' . $tdNum . ' font-bold text-orange-600">' . $cell($r['tracking_number'] ?? '') . '</td>';
        echo '<td class="' . $tdTitle . '">' . $cell($r['report_title'] ?? '') . '</td>';
        echo '<td class="' . $tdSubject . '">' . $cell($r['subject_matter'] ?? '') . '</td>';
        echo '<td class="' . $tdType . '">' . $cell($r['report_type_name'] ?? '') . '</td>';
        echo '<td class="' . $tdCommittees . '">' . $cell($r['committees_display'] ?? '') . '</td>';
        echo '<td class="' . $tdNum . '">' . $cell($r['date_display'] ?? '') . '</td>';
        echo '<td class="' . $tdNum . ' text-center font-medium">' . $cell(isset($r['cycle']) ? (string) $r['cycle'] : '') . '</td>';
        echo '<td class="' . $td . '">';
        echo '<span class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-full border ' . $bgClass . '">' . htmlspecialchars($statusName) . '</span>';
        echo '</td>';
        echo '<td class="' . $tdNum . ' font-medium">';
        echo '<button type="button" data-committee-report-id="' . $rid . '" data-document-id="' . $did . '" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition duration-150 cursor-pointer">View</button>';
        echo '</td>';
        echo '</tr>';
    }
}

function render_committee_reports_table_head(string $tableId): void
{
    $th = 'px-6 sm:px-10 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer align-bottom';
    $h = static function (int $col, string $label, string $extraClass = '') use ($tableId, $th): string {
        $cls = trim($th . ' ' . $extraClass);

        return '<th scope="col" class="' . $cls . '" onclick="sortTable(' . $col . ', \'' . htmlspecialchars($tableId, ENT_QUOTES) . '\')"><div class="flex items-center gap-2 whitespace-nowrap">' . htmlspecialchars($label)
            . ' <svg class="h-4 w-4 text-gray-400 sort-icon shrink-0" data-column="' . $col . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div></th>';
    };
    echo '<thead class="bg-slate-50"><tr>';
    echo $h(0, 'Committee Report #');
    echo $h(1, 'Joint Committee Report #');
    echo $h(2, 'Proposed No.');
    echo $h(3, 'Report Title', 'min-w-[28rem]');
    echo $h(4, 'Subject Matter', 'min-w-[28rem]');
    echo $h(5, 'Type', 'min-w-[12rem]');
    echo $h(6, 'Committees', 'min-w-[24rem]');
    echo $h(7, 'Date');
    echo $h(8, 'Cycle');
    echo $h(9, 'Status');
    echo '<th scope="col" class="px-6 sm:px-10 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap align-bottom">Action</th>';
    echo '</tr></thead>';
}

include '../../components/header.php';
?>
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="">
    <?php include '../../components/sidebar.php'; ?>

    <div class="flex-1 md:ml-64 min-h-screen">

        <?php
        $pageTitle = 'All Reports';
        include '../../components/topbar.php';
        ?>

        <main class="p-3 sm:p-6">
            <?php if ($showReportCreatedBanner): ?>
                <div id="reportCreatedBanner"
                    class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] w-max max-w-md animate-fade-in shadow-lg rounded-lg"
                    style="background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);">
                    <div class="flex items-center gap-3 px-6 py-4">
                        <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">Committee report created successfully.</span>
                        <button type="button" onclick="document.getElementById('reportCreatedBanner').remove()" class="text-current opacity-70 hover:opacity-100 transition ml-2 cursor-pointer" aria-label="Dismiss">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['route_status'])): ?>
                <div id="globalMessage"
                    class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 w-max max-w-md animate-fade-in shadow-lg rounded-lg"
                    style="<?php echo $_SESSION['route_status'] === 'success' ? 'background-color: rgb(220, 252, 231); color: rgb(20, 83, 45); border: 1px solid rgb(167, 243, 208);' : 'background-color: rgb(254, 226, 226); color: rgb(127, 29, 29); border: 1px solid rgb(252, 165, 165);'; ?>">
                    <div class="flex items-center gap-3 px-6 py-4">
                        <?php if ($_SESSION['route_status'] === 'success'): ?>
                            <svg class="h-5 w-5 shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        <?php endif; ?>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['route_message']); ?></span>
                        <button onclick="closeMessage()" class="text-current opacity-70 hover:opacity-100 transition ml-2">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php
                unset($_SESSION['route_status']);
                unset($_SESSION['route_message']);
                ?>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-2">
                <div class="p-4 border-b border-slate-200">
                    <div class="flex space-x-1 overflow-x-auto flex-nowrap pb-1 hide-scrollbar">
                        <button type="button" class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 <?php echo $initialTab === 'all' ? 'active text-blue-600 border-blue-600' : 'text-slate-600 border-transparent hover:text-slate-800'; ?>" data-tab="tab-all-reports" onclick="switchTab(this)">
                            All Reports
                        </button>
                        <button type="button" class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 <?php echo $initialTab === 'committee' ? 'active text-blue-600 border-blue-600' : 'text-slate-600 border-transparent hover:text-slate-800'; ?>" data-tab="tab-committee-reports" onclick="switchTab(this)">
                            Committee Reports
                        </button>
                        <button type="button" class="tab-button shrink-0 whitespace-nowrap px-4 py-2 text-sm font-medium border-b-2 <?php echo $initialTab === 'joint' ? 'active text-blue-600 border-blue-600' : 'text-slate-600 border-transparent hover:text-slate-800'; ?>" data-tab="tab-joint-committee-reports" onclick="switchTab(this)">
                            Joint Committee Reports
                        </button>
                    </div>
                </div>
            </div>

            <div id="tab-all-reports" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="<?php echo $initialTab === 'all' ? '' : 'display:none;'; ?>">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">All committee reports</h2>
                            <p class="text-sm text-gray-600 mt-1">One row per linked document; reports you created</p>
                        </div>
                        <div class="relative w-full min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" id="searchInput-all-reports" placeholder="Search..."
                                class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                oninput="filterTabTable('tableBody-all-reports', this.value)">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="table-all-reports" class="min-w-[2400px] w-full table-auto divide-y divide-slate-200">
                        <?php render_committee_reports_table_head('table-all-reports'); ?>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-all-reports">
                            <?php render_committee_report_table_rows($reportsRenderError, $reportsAllList); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-all-reports" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-all-reports" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <div id="tab-committee-reports" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="<?php echo $initialTab === 'committee' ? '' : 'display:none;'; ?>">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Committee reports</h2>
                            <p class="text-sm text-gray-600 mt-1">One row per document; non-joint report types</p>
                        </div>
                        <div class="relative w-full min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" id="searchInput-committee-reports" placeholder="Search..."
                                class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                oninput="filterTabTable('tableBody-committee-reports', this.value)">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="table-committee-reports" class="min-w-[2400px] w-full table-auto divide-y divide-slate-200">
                        <?php render_committee_reports_table_head('table-committee-reports'); ?>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-committee-reports">
                            <?php render_committee_report_table_rows($reportsRenderError, $reportsCommitteeList); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-committee-reports" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-committee-reports" class="flex items-center space-x-2"></div>
                </div>
            </div>

            <div id="tab-joint-committee-reports" class="tab-panel bg-white rounded-lg shadow-sm border border-slate-200" style="<?php echo $initialTab === 'joint' ? '' : 'display:none;'; ?>">
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Joint committee reports</h2>
                            <p class="text-sm text-gray-600 mt-1">One row per document; joint report types</p>
                        </div>
                        <div class="relative w-full min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" id="searchInput-joint-committee-reports" placeholder="Search..."
                                class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg outline-none w-full focus:ring-2 focus:ring-[#0033A1] focus:border-transparent"
                                oninput="filterTabTable('tableBody-joint-committee-reports', this.value)">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="table-joint-committee-reports" class="min-w-[2400px] w-full table-auto divide-y divide-slate-200">
                        <?php render_committee_reports_table_head('table-joint-committee-reports'); ?>
                        <tbody class="bg-white divide-y divide-slate-200" id="tableBody-joint-committee-reports">
                            <?php render_committee_report_table_rows($reportsRenderError, $reportsJointList); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="paginationInfo-joint-committee-reports" class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</div>
                    <div id="paginationContainer-joint-committee-reports" class="flex items-center space-x-2"></div>
                </div>
            </div>
        </main>

    </div>
</div>

<script src="/PangasinanLIS/src/js/global.js"></script>
<script src="/PangasinanLIS/src/js/page_transition.js"></script>
<script>
    function switchTab(btn) {
        document.querySelectorAll('.tab-button').forEach(function (b) {
            b.classList.remove('active', 'text-blue-600', 'border-blue-600');
            b.classList.add('text-slate-600', 'border-transparent');
        });
        btn.classList.add('active', 'text-blue-600', 'border-blue-600');
        btn.classList.remove('text-slate-600', 'border-transparent');
        document.querySelectorAll('.tab-panel').forEach(function (panel) {
            panel.style.display = 'none';
        });
        var targetId = btn.getAttribute('data-tab');
        var panel = document.getElementById(targetId);
        if (panel) {
            panel.style.display = '';
        }
    }

    function filterTabTable(tableBodyId, query) {
        var tbody = document.getElementById(tableBodyId);
        if (!tbody) return;
        var rows = tbody.querySelectorAll('tr:not(.empty-state)');
        var q = query.toLowerCase();
        rows.forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
        var mgr = window.paginationManagers && window.paginationManagers[tableBodyId];
        if (mgr) mgr.init();
    }

    document.addEventListener('DOMContentLoaded', function () {
        window.paginationManagers = {};
        var tabs = [
            { tableBodyId: 'tableBody-all-reports', paginationContainerId: 'paginationContainer-all-reports', infoDisplayId: 'paginationInfo-all-reports' },
            { tableBodyId: 'tableBody-committee-reports', paginationContainerId: 'paginationContainer-committee-reports', infoDisplayId: 'paginationInfo-committee-reports' },
            { tableBodyId: 'tableBody-joint-committee-reports', paginationContainerId: 'paginationContainer-joint-committee-reports', infoDisplayId: 'paginationInfo-joint-committee-reports' }
        ];
        tabs.forEach(function(cfg) {
            if (typeof PaginationManager !== 'undefined') {
                var mgr = new PaginationManager({
                    itemsPerPage: 10,
                    tableBodyId: cfg.tableBodyId,
                    paginationContainerId: cfg.paginationContainerId,
                    infoDisplayId: cfg.infoDisplayId
                });
                mgr.init();
                window.paginationManagers[cfg.tableBodyId] = mgr;
            }
            var tbody = document.getElementById(cfg.tableBodyId);
            if (tbody) {
                var rows = tbody.querySelectorAll('tr:not(.empty-state)');
                if (rows.length === 0 && typeof toggleEmptyState === 'function') {
                    toggleEmptyState(cfg.tableBodyId, true, {
                        title: "No reports found",
                        subtitle: "There's nothing to display here.",
                    });
                }
            }
        });
    });
</script>

</body>
</html>
