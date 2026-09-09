# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Agence Voyage CMS — a multi-tenant Laravel 12 CMS for travel agencies, centered around a custom vanilla-JS page builder with server-side Blade rendering. PHP 8.2+, Vite, Tailwind, SQLite for tests.

## Common Commands

```bash
# One-shot setup (install, .env, key, migrate, build)
composer setup

# Full dev stack: serve + queue:listen + pail logs + vite (concurrently)
composer dev

# Tests (clears config first, then runs artisan test)
composer test

# Run a single test file / filter
php artisan test --filter=PageServiceTest
php artisan test tests/Feature/Builder/CanonicalStructureTest.php

# PHPUnit directly (same config)
vendor/bin/phpunit --filter=methodName

# Frontend
npm run dev        # Vite dev server
npm run build      # Production build

# Lint / format PHP (Pint is installed dev dep)
vendor/bin/pint

# Tail Laravel logs
php artisan pail
```

Test DB is SQLite in-memory (`phpunit.xml`); no external DB needed for tests.

## High-Level Architecture

The system has two co-equal halves: a Laravel backend that owns data and Blade rendering, and a vanilla-JS **page builder** (`resources/js/builder/`) that drives an editor UI. They communicate via a single render endpoint and standard CRUD routes.

### Multi-Tenancy (critical, implicit everywhere)

Every domain table has `agency_id`. Tenant isolation is **automatic**, not manual:

- `App\Support\AgencyContext` holds the current agency id (set by `SetAgencyContext` middleware from the authed user).
- `App\Scopes\AgencyScope` is a global Eloquent scope applied to tenant models, filtering queries by `AgencyContext::get()`.
- Result: do NOT add `where('agency_id', …)` clauses in new code — rely on the scope. When writing CLI/queue code that runs outside a request, you must set the context manually (or use `withoutGlobalScope`) or queries will return nothing.
- Slug uniqueness (e.g. pages) is per-agency, not global.

### Page Builder: canonical "array-root" structure

The page builder stores the page tree as a **flat array at the root**, not an object:

```json
[ { "id": "...", "type": "hero", "props": {...}, "children": [...] }, ... ]
```

Legacy data shaped as `{ type: "page", children: [...] }` is migrated on read via `PageService::canonicalizeStructure()` / `ensureCanonicalStructure()`. New code must produce/expect array roots; renderer and validators reject non-list roots. Recent commits (`refactor(renderer): harden validation and enforce canonical array-root pages`) make this a hard invariant.

### Render pipeline

Editor and live frontend share the same renderer:

1. JS POSTs the structure to `POST /builder/render` with a `mode` (`editor` | `preview` | `live`).
2. `App\Services\PageRenderer` walks the tree. For each node it:
   - Resolves the component via `ComponentRegistry` (cached list of active components).
   - Validates props against the component schema (`ComponentValidator`).
   - Sanitizes props (XSS) and renders Blade view `components.builder.{type}` with `props`, `children` (pre-rendered HTML), `mode`, `isEditor`, `nodeId`.
   - Recurses into children, capped at depth 50.
3. Returns concatenated HTML; client injects into the canvas DOM.

Every builder component view should respect the `$isEditor` flag and the `$nodeId` data attribute so selection/overlay can re-bind after re-render.

### Builder JS runtime (`resources/js/builder/`)

Single entry: `resources/js/builder/index.js`, booted on `DOMContentLoaded`. Internally it is a small event-driven framework:

- `core/` — `store.js` (single source of truth: structure, selection, drag state), `event-bus.js`, `render-manager.js` (rAF-batched, version-stamped to drop stale responses), `structure-rules.js` (which node types may contain which).
- `canvas/` — server-render fetch, DOM injection, selection/hover/focus event binding, **selection restoration via `data-node-id`** after each render.
- `drag/` — `drag-engine` dispatches; `drag-validate` enforces `structure-rules`; `drag-hitbox` computes before/after/inside drop targets; `drag-reorder` mutates the store.
- `overlay/`, `selection/`, `settings/`, `right-sidebar.js`, `sidebar.js` — selection toolbar (move/duplicate/delete), props editor sidebar.
- `history/` — undo/redo stack + Ctrl+Z / Ctrl+Shift+Z bindings.
- `nodes/`, `runtime/`, `inline-editing.js` — tree ops, delegated runtime interactions (data-attribute dispatch), inline text editing.

Recent refactor commits centralize this: **interactions are delegated** (a single document-level listener dispatching by `data-*` attributes), and rendering is **mode-aware** (`editor` / `preview` / `live` lifecycle). When adding builder behavior, prefer extending the event-bus + delegated runtime over attaching ad-hoc listeners.

Render flow on any mutation:

```
mutate store → emit STRUCTURE_UPDATED → RenderManager.requestRender()
  → rAF-batched POST /builder/render → inject HTML → rebind → CANVAS_RENDERED
```

`RenderManager` deduplicates: a new request in-flight queues a "pending-after-current" render so rapid edits collapse into at most one trailing render.

### Versioning & publishing

`PageService` (in `app/Services`) is the only sanctioned way to mutate pages. It:

- Generates per-agency-unique slugs.
- On `update`/`updateStructure`, snapshots the previous structure into `PageVersion` only if structure actually changed.
- `restore(page, version)` snapshots current state before reverting.
- `publish` validates the structure (`PageStructureValidator`) before flipping status.

Controllers should call `PageService`, not `Page` directly, to keep versioning consistent.

### Authorization

- Spatie `laravel-permission` is installed but the app layers its own RBAC on top: `User → Role → Permission` (UUIDs, agency-scoped).
- `User::isAdmin()` checks role slug `admin`.
- A `Gate::before` returns `true` for admins, so admins bypass policies.
- `User::getPermissions()` and `Role` permission sync are cached (`user_permissions_v1_{id}`, 1h); role/permission mutations must call the cache-invalidation hooks on `Role`.

### Other caches to be aware of

- `components.registry` (1h) — invalidate when toggling `Component.is_active` or changing schemas.
- `page_{slug}` — invalidate when updating/publishing pages.

## Routes

Only two route files: `routes/web.php` (all app routes, session-auth) and `routes/console.php`. No `api.php` — Sanctum is installed but unused for routing. The render endpoint is web-routed (`POST /builder/render`) and CSRF-protected; the JS client must send `X-CSRF-TOKEN`.

## Conventions

- **UUID primary keys** throughout (`users`, `agencies`, `pages`, `components`, `roles`, `permissions`, `menus`). Use `$keyType = 'string'` / `$incrementing = false` on new tenant models.
- **Component Blade views** live in `resources/views/components/builder/{type}.blade.php` and accept the standard `@props(['props' => [], 'children' => '', 'mode' => 'live', 'isEditor' => false, 'nodeId' => null])`.
- **Vite entries** (`vite.config.js`): `resources/css/app.css`, `resources/js/app.js`, `resources/js/builder/index.js`. Add new top-level bundles here if you split builder code further.
- Tests are split into `tests/Unit` and `tests/Feature` (PHPUnit, not Pest, despite the pest plugin being allowed in composer config).

