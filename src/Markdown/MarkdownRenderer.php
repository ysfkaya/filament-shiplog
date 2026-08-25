<?php

namespace Ysfkaya\ShipLog\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContentInterface;

class MarkdownRenderer
{
    private readonly MarkdownConverter $converter;

    public function __construct(bool $allowRawHtml = false)
    {
        $environment = new Environment([
            'html_input' => $allowRawHtml ? 'allow' : 'escape',
            'allow_unsafe_links' => false,
        ]);

        $environment
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new GithubFlavoredMarkdownExtension)
            ->addExtension(new ShipLogExtension);

        $this->converter = new MarkdownConverter($environment);
    }

    public function toHtml(?string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        return trim($this->render($markdown)->getContent());
    }

    /**
     * Renders a fragment that is meant to sit inside an existing block, such
     * as a single bullet point, without its wrapping paragraph.
     */
    public function toInlineHtml(?string $markdown): string
    {
        $html = $this->toHtml($markdown);

        if (str_starts_with($html, '<p>') && str_ends_with($html, '</p>') && substr_count($html, '<p>') === 1) {
            return trim(substr($html, 3, -4));
        }

        return $html;
    }

    protected function render(string $markdown): RenderedContentInterface
    {
        return $this->converter->convert($markdown);
    }
}
