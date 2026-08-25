<?php

namespace Ysfkaya\ShipLog\Data;

use Ysfkaya\ShipLog\Enums\ChangeType;

/**
 * A single "### Added" style bucket of a release, holding already rendered
 * HTML fragments for each bullet point.
 */
final readonly class ChangeGroup
{
    /**
     * @param  array<int, string>  $items
     */
    public function __construct(
        public ChangeType $type,
        public array $items = [],
    ) {}

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array{type: string, label: string, accent: string, count: int, items: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'label' => $this->type->getLabel(),
            'accent' => $this->type->getAccent(),
            'count' => $this->count(),
            'items' => $this->items,
        ];
    }
}
