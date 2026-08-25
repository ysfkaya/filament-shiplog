<?php

namespace Ysfkaya\ShipLog\Repositories;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Ysfkaya\ShipLog\Contracts\ChangelogRepository;
use Ysfkaya\ShipLog\Data\Release;
use Ysfkaya\ShipLog\Markdown\ChangelogParser;

/**
 * Reads releases straight out of a CHANGELOG.md file. Order is whatever the
 * file says, because the person writing it already decided.
 */
class MarkdownChangelogRepository implements ChangelogRepository
{
    public function __construct(
        protected readonly ChangelogParser $parser,
        protected readonly Filesystem $files,
        protected readonly string $path,
    ) {}

    public function all(): Collection
    {
        if (! $this->files->isFile($this->path)) {
            return new Collection;
        }

        return new Collection($this->parser->parse($this->files->get($this->path)));
    }

    public function find(string $version): ?Release
    {
        return $this->all()->first(fn (Release $release): bool => $release->version === $version);
    }

    public function signature(): string
    {
        return $this->files->isFile($this->path)
            ? (string) $this->files->lastModified($this->path)
            : '';
    }

    public function path(): string
    {
        return $this->path;
    }
}
