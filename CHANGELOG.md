# Changelog

All notable changes to `filament-shiplog` are documented here. The format
follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- Markdown and database changelog drivers behind a `ChangelogRepository` contract.
- `<ship-log>` custom element rendering the timeline in a shadow root, with a floating button and a right hand sheet.
- Filament page and release resource, both behind the `shiplog.view` and `shiplog.manage` gates.
- Per release environment targeting, via `<!-- shiplog: environments: staging -->` or the resource form.
- Markdown extensions for GitHub style callouts, `^[tooltips](hint)` and lazily loaded images.
- Cursor paginated feed with infinite scroll, and `content-visibility` on release cards.
- Inertia support: injection skips `X-Inertia` visit responses.

> [!NOTE]
> A richly formatted sample changelog, useful for previewing every renderer
> feature, ships at `stubs/CHANGELOG.example.md`.
