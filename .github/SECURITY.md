# Security Policy

## Supported versions

| Version | Supported |
| ------- | --------- |
| 1.x     | Yes       |

## Reporting a vulnerability

Please do not open a public issue.

Report vulnerabilities through [GitHub's private advisory form](https://github.com/ysfkaya/filament-shiplog/security/advisories/new),
or by email to **yusuf.kaya.x0@gmail.com**. You should get a reply within a few
days, and a fix or a plan within two weeks for anything confirmed.

Please include the package version, a description of the impact, and steps to
reproduce.

## Things worth knowing before you report

Two behaviours look like vulnerabilities but are configuration:

- **The changelog is readable by anyone.** Visibility is decided by the
  `shiplog.view` gate, which defaults to any authenticated user. If a
  changelog is public, the gate was widened — check `->authorizeView()`.
- **HTML in a release renders.** Raw HTML is escaped unless
  `->allowRawHtml()` is enabled. Only turn it on for content you trust, since
  release bodies are injected into the page as HTML.

Genuine issues we do want to hear about: escaping bypasses in the markdown
pipeline, a way to read releases without passing the gate, and anything that
lets a non-admin write to the changelog.
