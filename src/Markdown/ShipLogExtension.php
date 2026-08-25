<?php

namespace Ysfkaya\ShipLog\Markdown;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Registers every Ship Log flavoured markdown behaviour in one place:
 * callout boxes, tooltips and lazy images.
 */
final class ShipLogExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addInlineParser(new TooltipParser, 100)
            ->addRenderer(Tooltip::class, new TooltipRenderer)
            ->addRenderer(BlockQuote::class, new AlertRenderer, 10)
            ->addRenderer(Image::class, new ImageRenderer, 10);
    }
}
