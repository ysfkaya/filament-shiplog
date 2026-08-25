<?php

namespace Ysfkaya\ShipLog\Markdown;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

/**
 * Parses `^[visible text](the hint)` into a Tooltip node.
 */
final class TooltipParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('\^\[([^\]]+)\]\(([^)]+)\)');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        [$label, $tip] = $inlineContext->getSubMatches();

        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());
        $inlineContext->getContainer()->appendChild(new Tooltip($label, $tip));

        return true;
    }
}
