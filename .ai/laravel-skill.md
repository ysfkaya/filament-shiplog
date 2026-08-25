---
name: filament-shiplog
description: Architecture, conventions and commands for the ysfkaya/filament-shiplog package — a changelog and release notes timeline for Filament v5. Load this before changing anything under lib/filament-shiplog.
---

# Ship Log — working on this package

A Filament v5 plugin that renders release notes as an animated timeline: a
floating button plus full-screen overlay on the frontend, and a page inside the
panel. Notes come from a `CHANGELOG.md` file or from the database.

## Ground rules

1. **Filament v5 and Laravel 12 only.** No v4 compatibility shims. Actions live in `Filament\Actions\`, layout components in `Filament\Schemas\Components\`, icons are the `Heroicon` enum.
2. **No new runtime dependencies.** `league/commonmark` is free because Laravel ships it. Anything else needs a strong argument.
3. **Run `vendor/bin/pint` before finishing.** Then `vendor/bin/phpstan` (level 5, clean) and `vendor/bin/pest`.
4. **Frontend CSS and JS never leave the shadow root.** No Tailwind, no Alpine, no build-time framework on the public side.
5. Explicit return types everywhere. PHPDoc array shapes for anything structured.

## Commands

```bash
composer test          # pest + pint + phpstan + rector
composer test:unit     # vendor/bin/pest
vendor/bin/pest --testsuite=Unit
vendor/bin/pest --testsuite=Feature
vendor/bin/pest --filter='parses a bare version'
vendor/bin/pint        # format (required before finishing)
vendor/bin/phpstan     # level 5, must stay clean
npm run build          # rebuild resources/dist/fi-shiplog.js — commit the output
npm run dev            # esbuild watch
```

`resources/dist/fi-shiplog.js` is **committed**. Editing `resources/js/` or
`resources/css/` without running `npm run build` ships a stale asset.

## Layout

```
src/
├── ShipLogServiceProvider.php   Bindings, gates, routes, assets, cache busting
├── ShipLogPlugin.php            Filament plugin (container singleton)
├── ShipLogManager.php           Driver resolution + environment filter + cache
├── Contracts/                   ChangelogRepository — the one real contract
├── Repositories/                Markdown and Database drivers
├── Markdown/                    CommonMark extensions + Keep a Changelog parser
├── Data/                        Release, ChangeGroup (readonly value objects)
├── Enums/                       ChangeType, FabPosition, ReleaseStatus
├── Models/                      Eloquent Release (database driver)
├── Support/                     Authorizer, FabSettings (shared singletons)
├── Concerns/                    Plugin traits: Authorization, HasFab, HasPage
├── Filament/                    Changelog page, ReleaseResource
├── Http/                        FeedController, InjectShipLog middleware
└── View/                        ShipLog Blade component (<x-shiplog />)
```

## The three decisions that shape everything

**1. The manager owns the rules, drivers own the data.**
`ChangelogRepository` implementations only fetch and map. Environment filtering
and caching live in `ShipLogManager`, so every driver — including one a user
registers with `ShipLog::extend()` — behaves identically without knowing the
rules exist. Put new cross-driver behaviour on the manager, never in a driver.

**2. Shared singletons, because the frontend is not in a panel.**
`Authorizer` and `FabSettings` are container singletons. The plugin's fluent
methods (`->authorizeView()`, `->fab()`) write into them rather than storing
local state, which is why a panel-level call also affects the floating button on
a public page. `ShipLogPlugin` itself is a singleton too, so
`ShipLogPlugin::make()` and `ShipLogPlugin::get()` are the same object.
Consequence: settings are global, not per-panel. Do not "fix" this by moving
state onto the plugin instance without solving the frontend case first.

**3. One renderer, two surfaces.**
The panel page and the public overlay both render `<ship-log>` — inline mode and
FAB mode of the same custom element, fed by the same `/shiplog/feed` JSON. There
is no server-rendered timeline. Never add a second Blade implementation of the
timeline; extend the element instead.

## Markdown pipeline

`MarkdownRenderer` builds a CommonMark environment with core, GFM and
`ShipLogExtension`, which registers:

| Feature  | Syntax                     | Implementation                                     |
| -------- | -------------------------- | -------------------------------------------------- |
| Alerts   | `> [!WARNING]`             | `AlertRenderer` overrides the core blockquote renderer at priority 10 and strips the marker from the AST |
| Tooltips | `^[label](hint)`           | `TooltipParser` (regex inline parser) + `Tooltip` node + `TooltipRenderer` |
| Images   | `![alt](src "title")`      | `ImageRenderer` adds `loading="lazy"`               |

Raw HTML is **escaped by default** (`shiplog.markdown.allow_html`). Every rich
feature has markdown syntax, so nobody needs to turn it on.

`ChangelogParser` splits a document on `##` headings, reads
`<!-- shiplog: environments: staging -->` directives, and collects `###` buckets
into `ChangeGroup` objects for the badges. `changes()` is public because the
database driver reuses it on markdown bodies typed into the panel.

Adding markdown syntax: write the node/parser/renderer under `src/Markdown/`,
register it in `ShipLogExtension`, add a case to
`tests/Unit/MarkdownRendererTest.php`, and style it in
`resources/css/fi-shiplog.css` with an `sl-` prefixed class.

## The custom element

`resources/js/fi-shiplog.js` defines `<ship-log>`. Attributes:

| Attribute     | Purpose                                                     |
| ------------- | ----------------------------------------------------------- |
| `src`         | Feed URL; fetched on first open                             |
| `mode`        | `fab` (default) or `inline` (used by the panel page)        |
| `position`    | `top-left` \| `top-right` \| `bottom-left` \| `bottom-right`|
| `label`       | Button text                                                 |
| `signature`   | Opaque change token; drives the unread dot via localStorage |
| `theme`       | Forces `dark`/`light`; omit to follow the host page         |
| `heading`, `subheading`, `empty-text`, `error-text` | Copy          |

Release bodies arrive as HTML the server already rendered and sanitised, and are
inserted with `innerHTML`. **Everything else must go through `escape()`.** If you
add a field to the payload, escape it at the point of use.

`window.ShipLog.{open,close,toggle,refresh}` is the public JS API. The element
dispatches `shiplog:open` / `shiplog:close` with `composed: true`.

## Authorization

Two gates, `shiplog.view` and `shiplog.manage`, defined by `Authorizer::registerGates()`
only when the application has not already defined them. `Authorizer::decide()`
prefers a plugin override, then falls back to the gate. Anything that gates
content must go through `Authorizer` — never call `Gate::allows()` directly from
a page, controller or view.

## Testing

`tests/TestCase.php` boots a Testbench app with a Filament panel fixture.

> **Provider order is load bearing.** Filament rebinds Livewire's `DataStore`
> mechanism, so `Filament\Support\SupportServiceProvider` must register before
> `Livewire\LivewireServiceProvider`. Reorder them and every Livewire render
> fails with `ViewErrorBag::put(): Argument #2 must be MessageBag, null given`.

Helpers in `tests/Pest.php`:

- `parser()` / `renderer()` — markdown pipeline without the container
- `changelogFixture($markdown)` — writes a temp changelog and points config at it
- `shiplog()` — a fresh `ShipLogManager`, so a test can switch drivers mid-run
- `actingAsUser()` — creates and authenticates a fixture user

The panel fixture calls `->resource()` explicitly, because the resource is
normally registered based on the driver at panel-registration time — too early
for a test body to influence.

## Common tasks

**Add a plugin option:** add a trait under `src/Concerns/`, use it in
`ShipLogPlugin`, and read it wherever it applies. If the frontend needs it, put
the state in a `Support/` singleton rather than on the plugin.

**Add a driver:** implement `ChangelogRepository` (three methods), add a
`create{Name}Driver()` to `ShipLogManager`, and cover it in
`tests/Feature/ShipLogManagerTest.php`.

**Change the timeline design:** `resources/css/fi-shiplog.css` for looks,
`#release()` in `resources/js/fi-shiplog.js` for markup. Run `npm run build`.
Keep `::part(fab)` and `::part(panel)` — they are the documented styling hooks.

**Add a field to a release:** migration stub, model casts, `Release` DTO,
`toArray()`, both repositories, the resource form and table, and the element's
`#release()` renderer. Escape it in the JS.
