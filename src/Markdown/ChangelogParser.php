<?php

namespace Ysfkaya\ShipLog\Markdown;

use Carbon\CarbonImmutable;
use Ysfkaya\ShipLog\Data\ChangeGroup;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\Enums\ChangeType;

/**
 * Turns a Keep a Changelog style document into Release objects.
 *
 * Every `##` heading starts a release; anything above the first one is
 * treated as a preamble and ignored.
 */
class ChangelogParser
{
    private const HEADING = '/^(?:\[(?P<bracketed>[^\]]+)\]|(?P<bare>\S+))(?:\((?P<link>[^)]*)\))?(?:\s*[-–—:]\s*(?P<date>\d{4}-\d{2}-\d{2}))?(?P<rest>.*)$/u';

    private const DIRECTIVE = '/<!--\s*shiplog:\s*(?P<body>.*?)\s*-->/is';

    public function __construct(
        protected readonly MarkdownRenderer $renderer,
    ) {}

    /**
     * @return array<int, Release>
     */
    public function parse(string $contents): array
    {
        return array_map(
            fn (array $section): Release => $this->toRelease($section['heading'], implode("\n", $section['lines'])),
            $this->split($contents),
        );
    }

    /**
     * @return array<int, array{heading: string, lines: array<int, string>}>
     */
    protected function split(string $contents): array
    {
        $sections = [];
        $index = -1;

        foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $contents)) as $line) {
            if (preg_match('/^##[ \t]+(?!#)(?P<heading>.*)$/', $line, $matches) === 1) {
                $sections[] = ['heading' => trim($matches['heading']), 'lines' => []];
                $index++;

                continue;
            }

            if ($index >= 0) {
                $sections[$index]['lines'][] = $line;
            }
        }

        return $sections;
    }

    protected function toRelease(string $heading, string $body): Release
    {
        preg_match(self::HEADING, $heading, $matches);

        $rest = trim($matches['rest'] ?? '');
        $yanked = preg_match('/\[?YANKED\]?/i', $rest) === 1;
        $version = filled($matches['bracketed'] ?? null) ? $matches['bracketed'] : ($matches['bare'] ?? $heading);

        $directives = $this->pullDirectives($body);

        return new Release(
            version: $version,
            title: $this->title($rest, $directives),
            releasedAt: $this->releasedAt($matches['date'] ?? null),
            body: $this->renderer->toHtml(trim($directives['body'])),
            changes: $this->changes($directives['body']),
            environments: $this->environments($directives),
            yanked: $yanked,
        );
    }

    /**
     * Strips `<!-- shiplog: key: value -->` comments out of the body and
     * returns them alongside the cleaned markdown.
     *
     * @return array<string, string>
     */
    protected function pullDirectives(string $body): array
    {
        $directives = [];

        if (preg_match_all(self::DIRECTIVE, $body, $matches) > 0) {
            foreach ($matches['body'] as $directive) {
                foreach (preg_split('/[;\n]+/', $directive) ?: [] as $pair) {
                    if (preg_match('/^\s*(?P<key>[\w-]+)\s*[:=]\s*(?P<value>.*?)\s*$/', $pair, $parts) === 1) {
                        $directives[mb_strtolower($parts['key'])] = $parts['value'];
                    }
                }
            }

            $body = (string) preg_replace(self::DIRECTIVE, '', $body);
        }

        $directives['body'] = $body;

        return $directives;
    }

    /**
     * @param  array<string, string>  $directives
     */
    protected function title(string $rest, array $directives): ?string
    {
        if (filled($directives['title'] ?? null)) {
            return $directives['title'];
        }

        $title = trim((string) preg_replace('/\[?YANKED\]?/i', '', $rest));
        $title = trim($title, " \t-–—:");

        return blank($title) ? null : $title;
    }

    protected function releasedAt(?string $date): ?CarbonImmutable
    {
        if (blank($date)) {
            return null;
        }

        $parsed = CarbonImmutable::createFromFormat('Y-m-d', $date);

        return $parsed instanceof CarbonImmutable ? $parsed->startOfDay() : null;
    }

    /**
     * @param  array<string, string>  $directives
     * @return array<int, string>
     */
    protected function environments(array $directives): array
    {
        $value = $directives['environments'] ?? $directives['environment'] ?? null;

        if (blank($value)) {
            return [];
        }

        return array_values(array_filter(array_map(trim(...), explode(',', $value))));
    }

    /**
     * Collects the `### Added` style buckets so the timeline can show badges
     * without re-reading the rendered body.
     *
     * @return array<int, ChangeGroup>
     */
    public function changes(string $body): array
    {
        $groups = [];
        $type = null;

        foreach (explode("\n", $body) as $line) {
            if (preg_match('/^###[ \t]+(?!#)(?P<heading>.*)$/', $line, $matches) === 1) {
                $type = ChangeType::fromHeading($matches['heading']);

                if ($type instanceof ChangeType && ! isset($groups[$type->value])) {
                    $groups[$type->value] = [];
                }

                continue;
            }

            if (! $type instanceof ChangeType) {
                continue;
            }

            if (preg_match('/^[-*+][ \t]+(?P<item>.+)$/', $line, $matches) === 1) {
                $groups[$type->value][] = $this->renderer->toInlineHtml($matches['item']);
            }
        }

        return array_map(
            fn (string $value, array $items): ChangeGroup => new ChangeGroup(ChangeType::from($value), $items),
            array_keys($groups),
            $groups,
        );
    }
}
