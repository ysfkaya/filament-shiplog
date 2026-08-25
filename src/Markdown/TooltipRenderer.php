<?php

namespace Ysfkaya\ShipLog\Markdown;

use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;

final class TooltipRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (! $node instanceof Tooltip) {
            throw new InvalidArgumentException('Expected a tooltip node.');
        }

        return new HtmlElement('span', [
            'class' => 'sl-tip',
            'tabindex' => '0',
            'role' => 'note',
            'data-sl-tip' => $node->tip,
        ], Xml::escape($node->label));
    }
}
