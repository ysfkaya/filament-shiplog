<?php

namespace Ysfkaya\ShipLog\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/**
 * Turns GitHub flavoured callouts into styled alert boxes:
 *
 *     > [!WARNING]
 *     > This release drops PHP 8.1 support.
 *
 * A blockquote without a marker keeps its default rendering.
 */
final class AlertRenderer implements NodeRendererInterface
{
    /**
     * @var array<string, string>
     */
    private const ALIASES = [
        'note' => 'info',
        'info' => 'info',
        'tip' => 'success',
        'success' => 'success',
        'important' => 'important',
        'warning' => 'warning',
        'caution' => 'danger',
        'danger' => 'danger',
    ];

    /**
     * @var array<string, string>
     */
    private const ICONS = [
        'info' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        'success' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'important' => 'M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18',
        'warning' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        'danger' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        BlockQuote::assertInstanceOf($node);

        $type = $this->pullType($node);
        $inner = $childRenderer->renderNodes($node->children());

        if ($type === null) {
            return '<blockquote>' . $inner . '</blockquote>';
        }

        return '<div class="sl-alert sl-alert--' . $type . '" role="note">'
            . $this->icon($type)
            . '<div class="sl-alert__body">' . $inner . '</div>'
            . '</div>';
    }

    /**
     * Reads the `[!TYPE]` marker off the first line and removes it from the tree.
     */
    private function pullType(BlockQuote $quote): ?string
    {
        $paragraph = $quote->firstChild();

        if (! $paragraph instanceof Paragraph) {
            return null;
        }

        $text = $paragraph->firstChild();

        if (! $text instanceof Text) {
            return null;
        }

        if (! preg_match('/^\[!([A-Za-z]+)\](.*)$/s', $text->getLiteral(), $matches)) {
            return null;
        }

        $type = self::ALIASES[mb_strtolower($matches[1])] ?? null;

        if ($type === null) {
            return null;
        }

        $remainder = ltrim($matches[2]);

        if ($remainder === '') {
            $newline = $text->next();
            $text->detach();

            if ($newline instanceof Newline) {
                $newline->detach();
            }
        } else {
            $text->setLiteral($remainder);
        }

        return $type;
    }

    private function icon(string $type): string
    {
        return '<svg class="sl-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">'
            . '<path stroke-linecap="round" stroke-linejoin="round" d="' . self::ICONS[$type] . '" />'
            . '</svg>';
    }
}
