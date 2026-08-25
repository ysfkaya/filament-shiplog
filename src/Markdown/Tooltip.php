<?php

namespace Ysfkaya\ShipLog\Markdown;

use League\CommonMark\Node\Inline\AbstractInline;

final class Tooltip extends AbstractInline
{
    public function __construct(
        public readonly string $label,
        public readonly string $tip,
    ) {
        parent::__construct();
    }
}
