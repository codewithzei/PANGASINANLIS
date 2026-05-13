<?php

// =========================================================================
// GLOBAL HELPER FUNCTIONS
// =========================================================================

/**
 * Kumukuha ng Tailwind CSS classes para sa status badges
 */
function getStatusBadgeClass($statusName) {
    $status = strtolower(trim($statusName));

    switch ($status) {

        case 'pending':
            return 'bg-amber-100 text-amber-800 border-amber-200';

        case 'approved':
            return 'bg-green-100 text-green-800 border-green-200';

        case 'withdrawn':
            return 'bg-gray-200 text-gray-800 border-gray-300';

        case 'deferred':
            return 'bg-orange-100 text-orange-800 border-orange-200';

        case 'noted':
            return 'bg-slate-100 text-slate-700 border-slate-200';

        case 'under processing':
            return 'bg-sky-100 text-sky-800 border-sky-200';

        case 'lay on the table':
            return 'bg-slate-200 text-slate-800 border-slate-300';

        case 'referred':
            return 'bg-indigo-100 text-indigo-800 border-indigo-200';

        case 'remanded':
            return 'bg-red-100 text-red-800 border-red-200';

        case 'returned to plenary':
            return 'bg-violet-100 text-violet-800 border-violet-200';

        case 'for committee report':
            return 'bg-cyan-100 text-cyan-800 border-cyan-200';

        case 'for opinion':
            return 'bg-yellow-100 text-yellow-800 border-yellow-200';

        case 'for calendar':
            return 'bg-pink-100 text-pink-800 border-pink-200';

        case 'on going':
            return 'bg-teal-100 text-teal-800 border-teal-200';

        default:
            return 'bg-blue-100 text-blue-800 border-blue-200';
    }
}

?>