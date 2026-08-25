# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this file doubles
as the sample data Ship Log ships with — every feature of the renderer appears
somewhere below.

## [Unreleased]

### Added
- Saved timeline filters, so you can pin the changelog to a single ^[change type](Added, Fixed, Security and friends — the buckets Keep a Changelog defines) and keep it that way between visits.
- An `shiplog:digest` command that emails the week's releases to your team.

### Changed
- The floating button now remembers its collapsed state per device.

> [!NOTE]
> Unreleased entries appear at the top of the timeline with no date badge. Drop
> this section before tagging, or keep it — Ship Log does not mind either way.

## [3.2.0] - 2026-08-14 — Timeline, redrawn

<!-- shiplog: environments: production, staging -->

The timeline got the redesign it has been asking for since 2.0. Cards breathe,
the rail animates as you scroll, and the whole thing finally looks like part of
your product instead of a bolted-on widget.

![The redesigned Ship Log timeline in dark mode](https://placehold.co/1200x640/0b1120/a5b4fc?text=Ship+Log+Timeline "The timeline in dark mode")

> [!TIP]
> Hold `Shift` while clicking the floating button to open the timeline
> scrolled to the oldest release. Handy when you are catching up after a break.

### Added
- A brand new ^[timeline rail](A gradient line linking every release, with a node per entry) that fills in as you scroll.
- Per-release environment targeting, so staging can preview what production has not seen yet.
- Keyboard navigation: `Esc` closes, `Tab` stays inside the panel.
- Support for images, tables and fenced code blocks inside release notes.

### Changed
- Release cards now lead with the version badge rather than the date.
- The unread dot is driven by a ^[content signature](The changelog file's modification time, or the newest updated_at in the database) instead of a version string, so editing an existing release counts as news.

### Fixed
- The overlay no longer traps scroll on iOS Safari when the page beneath is short.
- Tooltips flip above the cursor near the bottom edge of the viewport.

## [3.1.2] - 2026-07-28

> [!WARNING]
> This release drops support for PHP 8.1. Upgrade before pulling it, or pin to
> `3.1.1` until you can.

### Fixed
- Alert boxes rendered inside list items lost their icon.
- `shiplog.cache.store` was ignored when flushing, leaving stale entries behind on multi-store setups.

### Security
- Raw HTML in changelog files is now escaped by default. Set `shiplog.markdown.allow_html` to opt back in for trusted content.

## [3.1.1] - 2026-07-11 [YANKED]

> [!CAUTION]
> Pulled within hours of release: the database driver returned drafts to the
> public timeline. Skip straight to `3.1.2`.

### Fixed
- Nothing that survived. See above.

## [3.1.0] - 2026-06-30 — Write releases in the panel

Releases no longer have to live in a file. Switch the driver and the same
timeline reads from your database, with a full editor behind your existing
authorization.

![The release editor inside a Filament panel](https://placehold.co/1200x560/f8fafc/6366f1?text=Release+Editor "Editing a release in Filament")

### Added
- A `database` driver, with a Filament resource for creating and editing releases.
- Scheduling: give a release a future date and it stays hidden until then.
- A `draft` status for notes that are not ready to ship.

| Driver     | Source                | Editable in the panel | Best for                        |
| ---------- | --------------------- | --------------------- | ------------------------------- |
| `markdown` | `CHANGELOG.md`        | No                    | Teams who write notes in the PR |
| `database` | `shiplog_releases`    | Yes                   | Teams who publish from the app  |

### Changed
- `ChangelogRepository` gained a `signature()` method. Custom drivers need to implement it:

```php
public function signature(): string
{
    return (string) $this->lastModifiedAt()?->timestamp;
}
```

## [3.0.0] - 2026-05-02 — Shadow DOM everywhere

> [!IMPORTANT]
> The timeline moved into a shadow root. If you overrode Ship Log's styles with
> your own CSS, those rules no longer apply — use the `part` selectors instead.

### Added
- The `<ship-log>` custom element, usable from Blade, React, Vue or Svelte.
- `window.ShipLog.open()` for opening the timeline from your own navigation.
- `::part(fab)` and `::part(panel)` hooks for the two pieces worth restyling.

### Removed
- The jQuery build. It served us well.
- `shiplog.inline_styles`, which the shadow root makes meaningless.

### Deprecated
- `ShipLog::entries()` in favour of `ShipLog::releases()`. It will be removed in 4.0.

## [2.4.0] - 2026-03-18

<!-- shiplog: environments: staging -->

> [!NOTE]
> This entry is tagged for staging only, so it is invisible in production. It is
> here to prove environment targeting works — flip your `APP_ENV` and watch it
> appear.

### Added
- An experimental compact timeline, still behind a flag.

## [2.3.0] - 2026-02-04

### Added
- Turkish and German translations.
- `php artisan shiplog:flush` for clearing the cache from a deploy script.

### Fixed
- Dates now respect the application locale rather than always rendering in English.

## [1.0.0] - 2025-11-20 — First launch

Where it started: a floating button, a file, and a list.

### Added
- A `CHANGELOG.md` driver.
- A floating button with four corner positions.
- Gate backed visibility, so only the people you choose ever see the button.
