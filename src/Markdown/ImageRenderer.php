<?php

namespace Ysfkaya\ShipLog\Markdown;

use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders images lazily so a changelog packed with screenshots stays cheap
 * to open.
 */
final class ImageRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (! $node instanceof Image) {
            throw new InvalidArgumentException('Expected an image node.');
        }

        $attributes = [
            'class' => 'sl-img',
            'src' => $node->getUrl(),
            'alt' => $childRenderer->renderNodes($node->children()),
            'loading' => 'lazy',
            'decoding' => 'async',
        ];

        if (($title = $node->getTitle()) !== null) {
            $attributes['title'] = $title;
        }

        return new HtmlElement('img', $attributes, '', selfClosing: true);
    }
}
