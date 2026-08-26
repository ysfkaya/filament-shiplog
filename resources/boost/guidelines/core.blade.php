# Ship Log (ysfkaya/filament-shiplog)

A changelog and release notes timeline for Filament v5. Releases come from a
`CHANGELOG.md` file or the database, and render as a floating button plus a
right hand sheet on the frontend, and a page inside the panel.

## Configuration lives on the plugin, not in config

There is **no config file**. Every setting is a method on `ShipLogPlugin`, and
the frontend needs the plugin registered on a panel — routes, gates and the
driver are all configured there.

<code-snippet name="Registering and configuring Ship Log" lang="php">
use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\ShipLogPlugin;

$panel->plugin(
    ShipLogPlugin::make()
        ->usingMarkdown(base_path('CHANGELOG.md'))
        ->cache(true, ttl: 3600)
        ->perPage(20)
        ->fab(FabPosition::BottomRight)
        ->authorizeView(fn (?User $user): bool => $user !== null)
        ->authorizeManage(fn (User $user): bool => $user->isAdmin()),
);
</code-snippet>

Never write `config('shiplog.*')` — it does not exist. Read settings from
`app(Ysfkaya\ShipLog\Support\Settings::class)` instead.

## Showing the timeline

Add `<x-shiplog />` to a Blade layout, or append
`Ysfkaya\ShipLog\Http\InjectShipLog` to the `web` middleware group to inject it
into every HTML response. The middleware is Inertia aware.

Open it from JavaScript with `window.ShipLog.open()`. The element emits
`shiplog:open` and `shiplog:close`.

## Drivers

`markdown` (default) reads a Keep a Changelog file and is **read-only** — the
developer edits the file and commits it. `database` adds a Filament resource so
releases can be written in the panel, with draft status and scheduling.

Both return the same `Release` value objects, so the timeline never changes.

<code-snippet name="Reading releases in application code" lang="php">
use Ysfkaya\ShipLog\Facades\ShipLog;

ShipLog::releases();          // visible in the current environment
ShipLog::releases('staging'); // as staging would see it
ShipLog::latest();
ShipLog::flush();
</code-snippet>

## Writing release notes

Standard markdown, plus three extras. Raw HTML is escaped unless
`->allowRawHtml()` is on, so use these rather than HTML:

- Callouts: `> [!NOTE]`, `> [!TIP]`, `> [!IMPORTANT]`, `> [!WARNING]`, `> [!CAUTION]`
- Tooltips: `^[visible text](the hint)`
- Environment targeting: `<!-- shiplog: environments: staging, local -->` under the `##` heading

`###` headings named `Added`, `Changed`, `Deprecated`, `Removed`, `Fixed` or
`Security` become badges on the release card.

## Authorization

Two gates, `shiplog.view` and `shiplog.manage`, defined only if the application
has not already defined them. Always check through the `Authorizer`, never
`Gate::allows()` directly:

<code-snippet name="Checking access" lang="php">
use Ysfkaya\ShipLog\Support\Authorizer;

app(Authorizer::class)->canView();
app(Authorizer::class)->canManage();
</code-snippet>

A user who fails `shiplog.view` gets no markup at all and a 403 from the feed.

## Gotchas

- Run `php artisan filament:assets` after installing or upgrading, or the timeline never registers.
- Caching clears itself when a database release is saved, but **not** when a markdown file changes. Call `ShipLog::flush()` or use the Clear cache action.
- The timeline renders in a shadow root. Application CSS cannot style it; use `::part(fab)` and `::part(panel)`.
- In package tests, register `Filament\Support\SupportServiceProvider` before `Livewire\LivewireServiceProvider` — Filament rebinds Livewire's `DataStore`, and the wrong order makes every Livewire render fail on a null error bag.
