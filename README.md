# Ship Log

A changelog your users will actually read.

Ship Log turns release notes into a premium, animated timeline — a floating
button on your frontend, a full-screen overlay that opens in place, and a
dedicated page inside your Filament panel. Notes can come from a `CHANGELOG.md`
file or from your database, and nobody sees them unless you say so.

Built for **Filament v5** and **Laravel 12**.

---

## Highlights

- **Two drivers, one timeline.** Read from `CHANGELOG.md` or from the database, and switch with one config line.
- **Framework agnostic.** The timeline is a custom element rendered in a shadow root, so it drops into Blade, React, Vue or Svelte without a single style collision.
- **Gate backed.** Explicit Laravel gates decide who sees the button and who may edit releases.
- **Environment aware.** Ship a release to staging only, and production never learns it exists.
- **Rich notes.** Alert boxes, tooltips, images, tables and code — all from plain markdown.
- **No new dependencies.** `league/commonmark` already ships with Laravel.

---

## Installation

```bash
composer require ysfkaya/filament-shiplog
php artisan filament:assets
```

Publish what you need:

```bash
php artisan vendor:publish --tag=shiplog-config
php artisan vendor:publish --tag=shiplog-migrations   # only for the database driver
php artisan vendor:publish --tag=shiplog-views        # only if you want to reshape the markup
```

Register the plugin on any panel:

```php
use Ysfkaya\ShipLog\ShipLogPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(ShipLogPlugin::make());
}
```

That is enough to get a **Changelog** page in your panel, reading from
`base_path('CHANGELOG.md')`.

---

## Showing the timeline on your frontend

Add the component wherever you like — usually the layout:

```blade
<x-shiplog />
```

Or let the middleware do it for every HTML response, which is the easiest path
for a JavaScript frontend:

```php
// bootstrap/app.php
use Ysfkaya\ShipLog\Http\InjectShipLog;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        InjectShipLog::class,
    ]);
})
```

Both routes render the same element and honour the same authorization, so
nothing appears for a visitor who is not allowed to read the changelog.

### Opening it from your own UI

```js
window.ShipLog.open()     // open the overlay
window.ShipLog.close()
window.ShipLog.toggle()
window.ShipLog.refresh()  // re-fetch after publishing a release
```

The element also emits `shiplog:open` and `shiplog:close`, which bubble and
cross the shadow boundary:

```js
document.addEventListener('shiplog:open', () => analytics.track('changelog_opened'))
```

### React, Vue and Svelte

`<ship-log>` is a standard custom element, so it works as-is:

```jsx
export function Changelog() {
    return <ship-log src="/shiplog/feed" position="bottom-left" label="What's new" />
}
```

```vue
<template>
    <ship-log src="/shiplog/feed" position="bottom-left" />
</template>
```

Everything renders inside a shadow root: your CSS cannot reach in, and Ship
Log's cannot leak out. Only two pieces are restylable, on purpose:

```css
ship-log::part(fab)   { border-radius: 0.5rem; }
ship-log::part(panel) { max-width: 60rem; }
```

---

## Drivers

### Markdown (default)

Point it at any [Keep a Changelog](https://keepachangelog.com) document:

```php
'driver' => 'markdown',

'markdown' => [
    'path' => base_path('CHANGELOG.md'),
],
```

Every `##` heading starts a release. All of these parse:

```markdown
## [3.2.0] - 2026-08-14 — Timeline, redrawn
## [3.1.1] - 2026-07-11 [YANKED]
## [Unreleased]
## 1.0.0 - 2025-11-20
## [2.0.0](https://github.com/you/repo/compare/1.0.0...2.0.0) - 2026-01-01
```

### Database

```php
'driver' => 'database',
```

```bash
php artisan vendor:publish --tag=shiplog-migrations
php artisan migrate
```

A **Releases** resource appears in the panel with a markdown editor, a status
(draft or published), a release date, environment targeting and a yanked flag.
Future dated releases stay hidden until the day arrives, and the timeline cache
is flushed automatically whenever a release is saved or deleted.

### Your own driver

```php
use Ysfkaya\ShipLog\Facades\ShipLog;

ShipLog::extend('github', fn (): ChangelogRepository => new GitHubReleaseRepository(
    repository: 'laravel/framework',
));
```

```php
'driver' => 'github',
```

A driver implements three methods:

```php
interface ChangelogRepository
{
    public function all(): Collection;          // Release objects, newest first
    public function find(string $version): ?Release;
    public function signature(): string;        // changes when the changelog changes
}
```

`signature()` powers the unread dot on the floating button. Return anything
cheap that changes when the content does — a file modification time, a
`max(updated_at)`, an ETag.

---

## Authorization

Ship Log defines two gates, and leaves them alone if your application already
owns them:

| Gate             | Controls                                              |
| ---------------- | ----------------------------------------------------- |
| `shiplog.view`   | The floating button, the feed, the panel page          |
| `shiplog.manage` | The releases resource and the cache action             |

Both default to "any authenticated user". Override them the way you would any
gate:

```php
Gate::define('shiplog.view', fn (?User $user): bool => $user !== null);
Gate::define('shiplog.manage', fn (User $user): bool => $user->isAdmin());
```

Or fluently on the plugin, which applies to the panel **and** the frontend:

```php
ShipLogPlugin::make()
    ->authorizeView(fn (?User $user): bool => $user?->hasVerifiedEmail() ?? false)
    ->authorizeManage(fn (User $user): bool => $user->hasRole('admin'));
```

```php
ShipLogPlugin::make()->authorize(view: true, manage: false);  // both at once
```

> A visitor who fails `shiplog.view` gets nothing: no button, no markup, and a
> `403` from the feed. The button never hints at updates somebody cannot read.

---

## Environment awareness

Two independent controls.

**Which releases are visible**, set per release. In markdown:

```markdown
## [2.4.0] - 2026-03-18
<!-- shiplog: environments: staging, local -->

Still baking.
```

In the database, fill the **Environments** field. Leave it empty and the release
shows everywhere.

**Where the button appears at all:**

```php
ShipLogPlugin::make()->fabEnvironments(['production']);
```

```php
'fab' => [
    'environments' => ['production', 'staging'],
],
```

---

## Writing release notes

Standard markdown, plus three additions.

### Alert boxes

GitHub's callout syntax, in five flavours:

```markdown
> [!NOTE]
> Neutral context.

> [!TIP]
> Something helpful.

> [!IMPORTANT]
> Do not miss this.

> [!WARNING]
> This one breaks something.

> [!CAUTION]
> Be careful here.
```

`NOTE`/`INFO`, `TIP`/`SUCCESS` and `CAUTION`/`DANGER` are interchangeable. A
blockquote without a marker stays a blockquote.

### Tooltips

```markdown
The timeline renders in a ^[shadow root](An isolated DOM tree, so styles never collide).
```

Hover or focus to reveal. Fully keyboard accessible.

### Images

```markdown
![The redesigned timeline](/img/changelog/timeline.png "Dark mode")
```

Images render lazily, so a changelog full of screenshots stays cheap to open.

### Change groups

`###` headings matching a Keep a Changelog type become badges on the release
card:

```markdown
### Added
- Something new

### Fixed
- Something broken
```

Recognised types: `Added`, `Changed`, `Deprecated`, `Removed`, `Fixed`,
`Security`.

---

## Configuration

```php
return [
    'driver' => env('SHIPLOG_DRIVER', 'markdown'),

    'markdown' => [
        'path' => env('SHIPLOG_PATH'),          // defaults to base_path('CHANGELOG.md')
        'allow_html' => false,                  // raw HTML is escaped by default
    ],

    'model' => Ysfkaya\ShipLog\Models\Release::class,
    'table' => 'shiplog_releases',

    'cache' => [
        'enabled' => env('SHIPLOG_CACHE', false),
        'store' => env('SHIPLOG_CACHE_STORE'),
        'key' => 'shiplog.releases',
        'ttl' => 3600,
    ],

    'route' => [
        'enabled' => true,
        'prefix' => 'shiplog',
        'middleware' => ['web'],                // add 'auth' to lock the feed down further
    ],

    'fab' => [
        'enabled' => true,
        'position' => FabPosition::BottomRight,
        'environments' => [],
        'auto_inject' => false,
        'label' => null,
    ],

    'gates' => [
        'view' => 'shiplog.view',
        'manage' => 'shiplog.manage',
    ],
];
```

Turn caching on in production. Markdown is parsed once, and the cache clears
itself whenever a database release is saved.

---

## Plugin API

```php
use Filament\Support\Icons\Heroicon;
use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\ShipLogPlugin;

ShipLogPlugin::make()
    // Panel page
    ->slug('whats-new')
    ->pageTitle('Product updates')
    ->navigationLabel('Updates')
    ->navigationIcon(Heroicon::OutlinedSparkles)
    ->navigationGroup('Settings')
    ->navigationSort(90)
    ->usingPage(YourOwnChangelogPage::class)

    // Releases resource
    ->resource()                                  // force on; defaults to the database driver
    ->resource(false)                             // force off

    // Floating button
    ->fab(FabPosition::BottomLeft)
    ->fabLabel('What changed?')
    ->fabEnvironments(['production'])

    // Authorization
    ->authorizeView(fn (?User $user): bool => $user !== null)
    ->authorizeManage(fn (User $user): bool => $user->isAdmin());
```

`FabPosition` covers `TopLeft`, `TopRight`, `BottomLeft` and `BottomRight`.

---

## Reading releases in your own code

```php
use Ysfkaya\ShipLog\Facades\ShipLog;

ShipLog::releases();              // Collection<Release>, filtered for the current environment
ShipLog::releases('staging');     // as a specific environment would see it
ShipLog::latest();
ShipLog::find('3.2.0');
ShipLog::signature();
ShipLog::flush();
ShipLog::driver();                // the underlying ChangelogRepository
```

Each `Release` is a readonly object:

```php
$release->version;        // '3.2.0'
$release->title;          // 'Timeline, redrawn'
$release->releasedAt;     // ?CarbonImmutable
$release->body;           // rendered HTML
$release->changes;        // ChangeGroup[] — type and items
$release->environments;   // string[]; empty means everywhere
$release->yanked;         // bool
```

---

## Theming

The timeline follows whatever the host page already decided — a `dark` class on
`<html>`, a `data-theme` attribute, or the operating system preference — and
updates live when that changes. Force it if you would rather not:

```blade
<x-shiplog theme="dark" />
```

Animations respect `prefers-reduced-motion`.

---

## Testing

```bash
composer test          # pest, pint, phpstan, rector
composer test:unit
```

Testing the plugin inside your own application:

```php
use Ysfkaya\ShipLog\Filament\Pages\Changelog;

it('hides the changelog from visitors', function (): void {
    expect(Changelog::canAccess())->toBeFalse();

    $this->getJson('/shiplog/feed')->assertForbidden();
});
```

> [!NOTE]
> Filament rebinds Livewire's `DataStore` mechanism. In a package test suite,
> register `Filament\Support\SupportServiceProvider` **before**
> `Livewire\LivewireServiceProvider`, or component error bags resolve to `null`.

---

## Credits

- [Yusuf Kaya](https://github.com/ysfkaya)

## License

MIT. See [LICENSE.md](LICENSE.md).
