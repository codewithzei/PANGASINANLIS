# Reusable Empty State Component

A reusable empty state component for displaying "no data found" messages in tables when there's no data or search results.

## Overview

The `toggleEmptyState()` function creates a consistent, styled empty state row that can be used across any table in your application. It automatically handles:

- Creating and removing empty state rows
- Custom titles and subtitles
- Custom SVG icons
- Search result filtering
- Integration with pagination

## Function Signature

```javascript
toggleEmptyState(tableBodyId = 'tableBody', show = false, options = {})
```

### Parameters

- **tableBodyId** (string, default: `'tableBody'`)
  - The ID of the table body element where the empty state will be inserted

- **show** (boolean, default: `false`)
  - Whether to display the empty state. Pass `true` to show, `false` to hide

- **options** (object)
  - **title** (string, default: `"No data found"`)
    - The main message displayed
  - **subtitle** (string, default: `"Add your first data to get started"`)
    - The secondary message below the title
  - **icon** (string, default: smiley face SVG path)
    - SVG path for the icon (d attribute value)
  - **colspan** (number, optional, default: auto-detect)
    - Number of columns to span. If not provided, it will automatically detect based on the table structure

## Usage Examples

### Basic Usage - Initial Empty State

```javascript
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr:not(.empty-state)');
    
    if (rows.length === 0) {
        toggleEmptyState('tableBody', true, {
            title: "No data found",
            subtitle: "Add your first data to get started"
        });
    }
});
```

### Search Results Empty State

```javascript
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
    toggleEmptyState('tableBody', visibleCount === 0, {
        title: "No results found",
        subtitle: "Try adjusting your search terms"
    });
}
```

### Auto-Detection of Column Span

The `toggleEmptyState()` function now **automatically detects the number of columns** in your table. You don't need to manually specify `colspan` anymore!

**How it works:**
1. If `colspan` is provided in options, it uses that value
2. If not provided, it looks for the first non-empty row in the table and counts its columns
3. If no rows exist, it checks the table header (`<thead>`)
4. Falls back to 4 columns if unable to detect

**Example - Works with any column count:**

```javascript
// Table with 5 columns
toggleEmptyState('tableBody', true, {
    title: "No users found",
    subtitle: "Create your first user to get started"
    // colspan is auto-detected! No need to specify it
});

// Table with 7 columns
toggleEmptyState('usersTable', true, {
    title: "No results",
    subtitle: "Try searching again"
    // Still works! Automatically uses 7 columns
});

// You can still manually override if needed
toggleEmptyState('tableBody', true, {
    title: "Custom message",
    subtitle: "Description",
    colspan: 8  // Manually set to 8 columns
});
```

### Custom Icon Example

Available SVG paths from Heroicons:

**Magnifying Glass (Search):**
```javascript
'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'
```

**Document (Empty Records):**
```javascript
'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
```

**Inbox (Empty Inbox):**
```javascript
'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'
```

**Custom Icon Usage:**
```javascript
toggleEmptyState('tableBody', true, {
    title: "No items found",
    subtitle: "Your inbox is empty",
    icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4'
});
```

## CSS Classes

The empty state row is created with the following classes:

- `.empty-state-row` - The table row container
- `.empty-state` - Additional class for identification

**Note:** Always exclude empty state rows from pagination/filtering by using the `:not(.empty-state)` selector:

```javascript
document.querySelectorAll('#tableBody tr:not(.empty-state)')
```

## Implementation Checklist

When implementing in a new page:

1. ✅ Ensure the table body has an ID (default: `tableBody`)
2. ✅ Import `global.js` in your page (includes `toggleEmptyState` function)
3. ✅ Call `toggleEmptyState()` on page load if data is empty
4. ✅ Call `toggleEmptyState()` in search/filter functions
5. ✅ Use `:not(.empty-state)` selector when querying rows
6. ✅ Update pagination manager to ignore empty state rows

## Default Empty State Styling

```html
<td colspan="4" class="px-6 py-8 text-center text-gray-500">
    <div class="flex flex-col items-center justify-center">
        <svg class="h-12 w-12 text-gray-400 mb-4">
            <!-- Icon -->
        </svg>
        <p class="text-lg font-medium">{title}</p>
        <p class="text-sm mt-1">{subtitle}</p>
    </div>
</td>
```

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ with polyfills for Array methods
