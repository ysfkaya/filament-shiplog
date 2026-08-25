<?php

namespace Ysfkaya\ShipLog\Data;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * A driver agnostic representation of one changelog release.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class Release implements Arrayable
{
    /**
     * @param  array<int, ChangeGroup>  $changes
     * @param  array<int, string>  $environments  An empty list means "every environment".
     */
    public function __construct(
        public string $version,
        public ?string $title = null,
        public ?CarbonImmutable $releasedAt = null,
        public string $body = '',
        public array $changes = [],
        public array $environments = [],
        public bool $yanked = false,
        public string | int | null $id = null,
    ) {}

    public function isVisibleIn(string $environment): bool
    {
        if ($this->environments === []) {
            return true;
        }

        return in_array($environment, $this->environments, strict: true);
    }

    public function isUnreleased(): bool
    {
        return $this->releasedAt === null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id ?? $this->version,
            'version' => $this->version,
            'title' => $this->title,
            'released_at' => $this->releasedAt?->toIso8601String(),
            'released_at_label' => $this->releasedAt?->isoFormat('ll'),
            'body' => $this->body,
            'changes' => array_map(fn (ChangeGroup $group): array => $group->toArray(), $this->changes),
            'yanked' => $this->yanked,
        ];
    }
}
