# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working on code in this repository.

## Project Overview

**PangasinanLIS** — Legislative Information System for the Province of Pangasinan. A PHP/MySQL web application that manages document routing, tracking, and workflow across different government offices and roles.

## Tech Stack

- **Backend:** Plain PHP (no framework), PDO for database access
- **Frontend:** Tailwind CSS v4 (via `@tailwindcss/cli`), vanilla JavaScript
- **Database:** MySQL (database name: `pangasinan-lis`)
- **Server:** Apache with `mod_rewrite` (clean URLs via `.htaccess`)

## Build & Development Commands

```bash
# Build CSS (one-time)
npx @tailwindcss/cli -i ./src/input.css -o ./dist/css/output.css

# Build CSS with watch mode (during development)
npm run dev
```

There is no test suite, no linter, and no bundler. JS is plain browser-side code.

## URL Routing

Clean URLs are handled by `.htaccess` mod_rewrite rules:

| URL path | Resolves to |
|---|---|
| `/login` | `pages/auth/login.php` |
| `/logout` | `includes/auth_logout.php` |
| `/dashboard` | `pages/dashboard.php` |
| Any `/path` | `path.php` if that file exists |

All internal links in the codebase use the full `/PangasinanLIS/` prefix (e.g., `/PangasinanLIS/pages/admin/external_offices`).

## Architecture

### Role-Based Access

The system has **5 roles**, each with their own page section:

| Role | Pages directory | Description |
|---|---|---|
| Super Admin | `pages/super_admin/` | User accounts, doc types, system config |
| Admin | `pages/admin/` | Document creation, routing, offices, hospitals |
| SP Secretary | `pages/sp_secretary/` | Inbox, received docs, routing |
| Committee | `pages/committee/` | Hearings, opinions, reports |
| Client | `pages/client/` | (Minimal — mostly empty) |

Authentication is session-based. Every protected page checks `$_SESSION['user_id']` and redirects to login if absent. The `role_name` session variable controls which dashboard/sidebar items render.

### Directory Structure

```
classes/                  # Business logic classes (PDO-based)
  document.php            # Core document CRUD, tracking number generation, routing
  user.php                # User CRUD (user_accounts + user_info tables)
  audit_logger.php        # Audit trail logging
  committee_report.php    # Committee report logic
  requirement.php         # Document requirement/checklist logic
  (+ simple lookup classes: doc_type, doc_stat, role, hospital, etc.)

components/               # Shared UI components (included via PHP)
  header.php              # HTML head, loads Tailwind CSS output + Roboto font
  sidebar.php             # Role-aware navigation sidebar with badge counts
  topbar.php              # Page header bar with sidebar toggle

includes/
  db.php                  # PDO connection (localhost, root, no password)
  auth_login.php          # Login form handler
  auth_logout.php         # Session destroy + redirect
  badges.php              # Badge/counter helpers
  fetch_*.php             # Data-fetching endpoints (return JSON for dropdowns/tables)
  actions_*/              # Form action handlers per role (CRUD operations)
  global/                 # Global auth helpers
  sp_secretary/           # SP Secretary-specific includes

pages/
  dashboard.php           # Role-conditional dashboard (Super Admin / Admin / default)
  auth/login.php          # Login page (standalone, no sidebar)
  admin/                  # Admin pages
  super_admin/            # Super Admin pages
  sp_secretary/           # SP Secretary pages
  committee/              # Committee pages
  plenary/                # (Empty)
  client/                 # (Minimal)

src/
  input.css               # Tailwind CSS entry point
  js/global.js            # Shared JS: sort, search, pagination, modals, empty states
  js/page_transition.js   # Page transition animations

dist/css/output.css       # Compiled Tailwind CSS (committed to repo)
```

### Page Pattern

Every authenticated page follows this pattern:
1. Start session, check `$_SESSION['user_id']` (redirect if missing)
2. Include `components/header.php` (opens `<html>`, loads CSS)
3. Include `components/sidebar.php` (role-based nav)
4. Include `components/topbar.php` (page title, notifications)
5. Page-specific content (tables, forms, modals)
6. Include `src/js/global.js` (or page-specific JS)
7. Close `</body></html>`

### Data Flow

- **Form submissions** → `includes/actions_*/` handlers → `classes/` business logic → Database
- **Table data loading** → `includes/fetch_*.php` endpoints return JSON → rendered server-side in PHP pages
- **Dropdown/population data** → `includes/fetch_*_data.php` files query lookup tables and return `<option>` HTML or JSON

### Database Schema

Key tables (from `newest.sql`):
- `user_accounts` — login credentials, role FK, account status
- `user_info` — profile details (name, contact, profile picture)
- `user_roles` — role lookup
- `documents` — core document table with tracking number, routing, ownership, status
- `document_types`, `document_statuses` — lookup tables
- `routing_options` — routing destinations (e.g., SP Secretary office)
- `external_offices`, `hospitals`, `muni_cities` — reference data
- `communication_categories` — categorization for "Noted" documents
- `committee_reports`, `committee_hearings` — committee workflow
- `audit_logs` — audit trail
- `requirements`, `document_requirements` — document checklist system

### Key Business Logic

- **Tracking numbers** are auto-generated in `YYYY-NNNNN` format (e.g., `2026-00001`) by `Document::generateTrackingNumber()`
- **Document routing:** Documents can be in-transit (`current_owner_user_id IS NULL`) or owned by a user. Routing is determined by `current_routing_option_id`.
- **"Noted" documents** (those with a `communication_category_id`) are immediately assigned to the creating Admin; others are in-transit to the SP Secretary.
- **Soft deletes** use `is_deleted` flag on several tables.

## Frontend Patterns

### Shared JavaScript (`src/js/global.js`)

- `sortTable(columnIndex, tableId)` — client-side table sorting with per-table state
- `filterTable()` — client-side search filtering
- `toggleEmptyState(tableBodyId, show, options)` — reusable empty state for tables
- `toggleEmptyStateContainer(containerId, show, options)` — same for non-table containers
- `openModal(modalId)` / `closeModal(modalId)` — modal show/hide
- `showEndorseToast(message, type)` — success/error toast notifications
- `PaginationManager` class — client-side pagination (10 items/page default)
- Sidebar toggle with collapse/expand and mobile responsive behavior

### Tailwind CSS

- Custom color: `#0033A1` (primary blue), `#E2F0FF` (light blue background)
- Custom font: Roboto
- Custom animations: `fade-in`, `fade-out`
- Custom z-index: `9999`

## Important Notes

- Database credentials are hardcoded in `includes/db.php` (root, no password) — this is a local development setup
- The `newest.sql` file in the root is the database schema dump
- File uploads go to `assets/uploads/` (with subdirectories like `assets/uploads/opinions/`)
- The sidebar queries the database for inbox/received counts inline — it requires `$pdo` to be available in scope
- Comments in the codebase are a mix of English and Filipino/Tagalog
