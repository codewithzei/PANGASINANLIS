// Sorting functionality
// Per-table sort state (keyed by tableId)
const _sortState = {};

function sortTable(columnIndex, tableId) {
    // Support both: sortTable(col, tableId) for multi-table pages
    // and legacy: sortTable(col) which falls back to #usersTable / #tableBody
    const table = tableId
        ? document.getElementById(tableId)
        : document.getElementById('usersTable');

    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-state)'));

    // Scope sort icons to this table only
    const icons = table.querySelectorAll('.sort-icon');

    // Per-table sort state
    const key = tableId || 'usersTable';
    if (!_sortState[key]) {
        _sortState[key] = { column: -1, direction: 1 };
    }
    const state = _sortState[key];

    // Reset all icons in this table
    icons.forEach(icon => {
        icon.innerHTML = `<path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />`;
    });

    // Toggle direction if same column clicked, otherwise reset to ascending
    if (state.column === columnIndex) {
        state.direction *= -1;
    } else {
        state.direction = 1;
    }
    state.column = columnIndex;

    // Update the clicked column's sort icon
    const currentIcon = icons[columnIndex];
    if (currentIcon) {
        if (state.direction === 1) {
            currentIcon.innerHTML = `<path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />`;
        } else {
            currentIcon.innerHTML = `<path fill-rule="evenodd" d="M10 17a1 1 0 01-.707-.293l-3-3a1 1 0 011.414-1.414L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3A1 1 0 0110 17zm3.707-9.293a1 1 0 00-1.414 0L10 9.586 7.707 7.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />`;
        }
    }

    rows.sort((a, b) => {
        const aText = a.cells[columnIndex] ? a.cells[columnIndex].textContent.trim() : '';
        const bText = b.cells[columnIndex] ? b.cells[columnIndex].textContent.trim() : '';

        // Use natural / numeric-aware locale compare.
        // This correctly handles:
        //   - Plain numbers (e.g. "5" vs "12")
        //   - Tracking numbers like "2026-00001" vs "2026-00099"
        //     (parseFloat would only read "2026" for both — wrong!)
        return aText.localeCompare(bText, undefined, { numeric: true, sensitivity: 'base' }) * state.direction;
    });

    // Reappend sorted rows
    rows.forEach(row => tbody.appendChild(row));

    // Re-initialize pagination for this table if a PaginationManager exists
    const mgr = window.paginationManagers && window.paginationManagers[tbody.id];
    if (mgr) mgr.init();
}



// ============================================
// REUSABLE EMPTY STATE COMPONENT
// ============================================

/**
 * Auto-detect the number of columns in a table
 * @param {string} tableBodyId - ID of the table body element
 * @returns {number} Number of columns (default: 4 if unable to detect)
 */
function getTableColspan(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) return 4;

    // Try to get colspan from first non-empty row
    const firstRow = tableBody.querySelector('tr:not(.empty-state)');
    if (firstRow) {
        return firstRow.querySelectorAll('td').length;
    }

    // Fallback to checking the table header if exists
    const table = tableBody.closest('table');
    if (table) {
        const thead = table.querySelector('thead tr');
        if (thead) {
            return thead.querySelectorAll('th, td').length;
        }
    }

    // Default fallback
    return 4;
}

/**
 * Display or hide empty state message
 * @param {string} tableBodyId - ID of the table body element
 * @param {boolean} show - Whether to show the empty state
 * @param {object} options - Configuration options
 *   - title: String (default: "No data found")
 *   - subtitle: String (default: "Add your first data to get started")
 *   - icon: String SVG path (default: smiley face)
 *   - colspan: Number (default: auto-detect from first row)
 */
function toggleEmptyState(tableBodyId = 'tableBody', show = false, options = {}) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) return;

    // Remove existing empty state if present
    const existingEmptyState = tableBody.querySelector('.empty-state-row');
    if (existingEmptyState) {
        existingEmptyState.remove();
    }

    if (!show) return;

    // Default options
    const config = {
        title: options.title || "No data found",
        subtitle: options.subtitle || "Add your first data to get started",
        icon: options.icon || 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        colspan: options.colspan || getTableColspan(tableBodyId)
    };

    // Create empty state row
    const emptyStateRow = document.createElement('tr');
    emptyStateRow.className = 'empty-state-row empty-state';
    emptyStateRow.innerHTML = `
        <td colspan="${config.colspan}" class="px-6 py-8 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center">
                <svg class="h-12 w-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}" />
                </svg>
                <p class="text-lg font-medium">${config.title}</p>
                <p class="text-sm mt-1">${config.subtitle}</p>
            </div>
        </td>
    `;

    tableBody.appendChild(emptyStateRow);
}

/**
 * Same visual pattern as toggleEmptyState, for a block container (e.g. card list, not tbody).
 * Injected node uses classes empty-state-block and empty-state so it can be removed on re-run.
 */
function toggleEmptyStateContainer(containerId = '', show = false, options = {}) {
    const root = document.getElementById(containerId);
    if (!root) return;

    const existing = root.querySelector('.empty-state-block');
    if (existing) {
        existing.remove();
    }

    if (!show) return;

    const config = {
        title: options.title || 'No data found',
        subtitle: options.subtitle || 'Add your first data to get started',
        icon: options.icon || 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };

    const block = document.createElement('div');
    block.className = 'empty-state-block empty-state';
    block.innerHTML = `
        <div class="px-6 py-8 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center">
                <svg class="h-12 w-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}" />
                </svg>
                <p class="text-lg font-medium">${config.title}</p>
                <p class="text-sm mt-1">${config.subtitle}</p>
            </div>
        </div>
    `;

    root.appendChild(block);
}

// Search functionality
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr:not(.empty-state)');
    let visibleCount = 0;

    rows.forEach(row => {
        let found = false;
        const cells = row.querySelectorAll('td');

        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(filter)) {
                found = true;
            }
        });

        if (found) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show empty state if no results found
    const emptyStateOptions = {
        title: "No results found",
        subtitle: "Try adjusting your search terms"
    };
    toggleEmptyState('tableBody', visibleCount === 0, emptyStateOptions);

    // Reset pagination after filtering
    if (paginationManager) {
        paginationManager.updateAfterFilter();
    }
}

// Initialize sort icons
document.addEventListener('DOMContentLoaded', function () {
    const sortIcons = document.querySelectorAll('.sort-icon');
    sortIcons.forEach(icon => {
        icon.style.cursor = 'pointer';
    });
});




// Iisang function para sa LAHAT ng modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden'); // Ipakita ang modal
        document.body.style.overflow = 'hidden'; // I-lock ang scroll ng background
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');    // Itago ang modal
        document.body.style.overflow = 'auto'; // I-enable ulit ang scroll
    }
}

// Close message notification
function closeMessage() {
    const message = document.getElementById('globalMessage');
    if (message) {
        message.classList.add('animate-fade-out');
        setTimeout(() => {
            message.remove();
        }, 300);
    }
}

// Auto-close message after 5 seconds
document.addEventListener('DOMContentLoaded', function () {
    const globalMessage = document.getElementById('globalMessage');
    if (globalMessage) {
        setTimeout(() => {
            closeMessage();
        }, 5000);
    }
});




// ============================================
// REUSABLE PAGINATION FUNCTIONALITY
// ============================================

class PaginationManager {
    constructor(options = {}) {
        this.itemsPerPage = options.itemsPerPage || 10;
        this.currentPage = 1;
        this.totalItems = 0;
        this.totalPages = 0;
        this.tableBodyId = options.tableBodyId || 'tableBody';
        this.paginationContainerId = options.paginationContainerId || 'paginationContainer';
        this.infoDisplayId = options.infoDisplayId || 'paginationInfo';
        this.allRows = [];
    }

    init() {
        // Get all rows from the table body
        const tableBody = document.getElementById(this.tableBodyId);
        if (!tableBody) {
            console.error(`Table body with id "${this.tableBodyId}" not found`);
            return;
        }

        this.allRows = Array.from(tableBody.querySelectorAll('tr:not(.empty-state)'));
        this.totalItems = this.allRows.length;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        this.currentPage = 1;

        this.renderPage(1);
    }

    renderPage(pageNumber) {
        // Validate page number
        if (pageNumber < 1 || pageNumber > this.totalPages) {
            return;
        }

        this.currentPage = pageNumber;
        const tableBody = document.getElementById(this.tableBodyId);
        const startIndex = (pageNumber - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        // Hide all rows
        this.allRows.forEach(row => row.style.display = 'none');

        // Show only current page rows
        this.allRows.slice(startIndex, endIndex).forEach(row => row.style.display = '');

        // Update pagination buttons and info
        this.updatePaginationUI();
    }

    updatePaginationUI() {
        this.updatePaginationInfo();
        this.updatePaginationButtons();
    }

    updatePaginationInfo() {
        const infoDisplay = document.getElementById(this.infoDisplayId);
        if (!infoDisplay) return;

        const startItem = this.totalItems === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);

        infoDisplay.innerHTML = `
            Showing <span class="font-medium">${startItem}</span> to 
            <span class="font-medium">${endItem}</span> of 
            <span class="font-medium">${this.totalItems}</span> results
        `;
    }

    updatePaginationButtons() {
        const container = document.getElementById(this.paginationContainerId);
        if (!container) return;

        // Clear existing buttons
        container.innerHTML = '';

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = this.currentPage === 1
            ? 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-400 cursor-not-allowed'
            : 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-700 hover:bg-slate-50 cursor-pointer';
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = this.currentPage === 1;
        prevBtn.onclick = () => this.currentPage > 1 && this.renderPage(this.currentPage - 1);
        container.appendChild(prevBtn);

        // Page number buttons
        const maxButtons = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(this.totalPages, startPage + maxButtons - 1);
        startPage = Math.max(1, endPage - maxButtons + 1);

        if (startPage > 1) {
            const firstBtn = document.createElement('button');
            firstBtn.className = 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-700 hover:bg-slate-50 cursor-pointer';
            firstBtn.textContent = '1';
            firstBtn.onclick = () => this.renderPage(1);
            container.appendChild(firstBtn);

            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'px-2 text-gray-700';
                dots.textContent = '...';
                container.appendChild(dots);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = i === this.currentPage
                ? 'px-3 py-1 bg-[#0033A1] text-white rounded text-sm font-medium hover:bg-blue-800'
                : 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-700 hover:bg-slate-50 cursor-pointer';
            pageBtn.textContent = i;
            pageBtn.onclick = () => this.renderPage(i);
            container.appendChild(pageBtn);
        }

        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'px-2 text-gray-700';
                dots.textContent = '...';
                container.appendChild(dots);
            }

            const lastBtn = document.createElement('button');
            lastBtn.className = 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-700 hover:bg-slate-50 cursor-pointer';
            lastBtn.textContent = this.totalPages;
            lastBtn.onclick = () => this.renderPage(this.totalPages);
            container.appendChild(lastBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = this.currentPage === this.totalPages
            ? 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-400 cursor-not-allowed'
            : 'px-3 py-1 border border-slate-300 rounded text-sm font-medium text-gray-700 hover:bg-slate-50 cursor-pointer';
        nextBtn.textContent = 'Next';
        nextBtn.disabled = this.currentPage === this.totalPages;
        nextBtn.onclick = () => this.currentPage < this.totalPages && this.renderPage(this.currentPage + 1);
        container.appendChild(nextBtn);
    }

    // Update pagination after filtering
    updateAfterFilter() {
        const tableBody = document.getElementById(this.tableBodyId);
        this.allRows = Array.from(tableBody.querySelectorAll('tr:not(.empty-state):not([style*="display: none"])')).map(row => {
            // Re-collect visible rows
            return row;
        });

        // Get all rows again but only count visible ones
        const allTableRows = Array.from(tableBody.querySelectorAll('tr'));
        const visibleRows = allTableRows.filter(row => row.style.display !== 'none');

        this.allRows = visibleRows;
        this.totalItems = visibleRows.length;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        this.currentPage = 1;
        this.renderPage(1);
    }
}

// Global pagination instance (will be initialized in each page)
let paginationManager = null;



// ============================================
// SIDEBAR TOGGLE FUNCTIONALITY
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.md\\:ml-64');

    if (!toggleBtn || !sidebar) return;

    let isCollapsed = false;

    toggleBtn.addEventListener('click', function () {
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            // Mobile: slide in/out as overlay
            sidebar.classList.toggle('mobile-show');
        } else {
            // Desktop: collapse to icon-rail
            isCollapsed = !isCollapsed;

            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
                if (mainContent) {
                    mainContent.classList.add('main-collapsed');
                }
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                if (mainContent) {
                    mainContent.classList.remove('main-collapsed');
                }
                // I-recalculate ang maxHeight ng open na dataManagementMenu
                // pagkatapos matapos ang sidebar transition — walang delay
                sidebar.addEventListener('transitionend', function onExpand(e) {
                    if (e.propertyName !== 'width') return;
                    sidebar.removeEventListener('transitionend', onExpand);
                    const menu = document.getElementById('dataManagementMenu');
                    if (menu && menu.style.maxHeight && menu.style.maxHeight !== '0px') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                });
            }
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (e) {
        const isMobile = window.innerWidth < 768;
        if (isMobile && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('mobile-show');
        }
    });

    // Clean up desktop formatting if window is resized to mobile viewport
    window.addEventListener('resize', function () {
        const isMobile = window.innerWidth < 768;
        if (isMobile && sidebar.classList.contains('sidebar-collapsed')) {
            sidebar.classList.remove('sidebar-collapsed');
            isCollapsed = false;
            if (mainContent) {
                mainContent.classList.remove('main-collapsed');
            }
        }
    });
});