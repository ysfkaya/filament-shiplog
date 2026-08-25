<?php

namespace Ysfkaya\ShipLog\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ysfkaya\ShipLog\Contracts\ChangelogRepository;
use Ysfkaya\ShipLog\Data\Release as ReleaseData;
use Ysfkaya\ShipLog\Markdown\ChangelogParser;
use Ysfkaya\ShipLog\Markdown\MarkdownRenderer;
use Ysfkaya\ShipLog\Models\Release;

/**
 * Reads published releases from the database and renders their markdown
 * bodies with the exact same pipeline the file driver uses.
 */
class DatabaseChangelogRepository implements ChangelogRepository
{
    public function __construct(
        protected readonly ChangelogParser $parser,
        protected readonly MarkdownRenderer $renderer,
        protected readonly string $model,
    ) {}

    public function all(): Collection
    {
        return $this->query()
            ->published()
            ->orderByDesc('released_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Release $release): ReleaseData => $this->toData($release));
    }

    public function find(string $version): ?ReleaseData
    {
        $release = $this->query()->published()->where('version', $version)->first();

        return $release instanceof Release ? $this->toData($release) : null;
    }

    /**
     * @return Builder<Release>
     */
    protected function query(): Builder
    {
        return $this->model::query();
    }

    protected function toData(Release $release): ReleaseData
    {
        $body = (string) $release->body;

        return new ReleaseData(
            version: $release->version,
            title: $release->title,
            releasedAt: $release->released_at,
            body: $this->renderer->toHtml($body),
            changes: $this->parser->changes($body),
            environments: $release->environments ?? [],
            yanked: (bool) $release->yanked,
            id: $release->getKey(),
        );
    }
}
