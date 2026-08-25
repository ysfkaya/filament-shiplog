<?php

it('turns github callouts into alert boxes', function (string $marker, string $class): void {
    $html = renderer()->toHtml("> [!{$marker}]\n> Careful now.");

    expect($html)->toContain("sl-alert sl-alert--{$class}")
        ->and($html)->toContain('Careful now.')
        ->and($html)->not->toContain("[!{$marker}]");
})->with([
    ['NOTE', 'info'],
    ['INFO', 'info'],
    ['TIP', 'success'],
    ['IMPORTANT', 'important'],
    ['WARNING', 'warning'],
    ['CAUTION', 'danger'],
]);

it('keeps the text that follows a marker on the same line', function (): void {
    $html = renderer()->toHtml('> [!WARNING] Right here.');

    expect($html)->toContain('sl-alert--warning')
        ->and($html)->toContain('Right here.');
});

it('leaves a plain blockquote alone', function (): void {
    expect(renderer()->toHtml('> Just a quote.'))
        ->toContain('<blockquote>')
        ->not->toContain('sl-alert');
});

it('ignores an unknown callout marker', function (): void {
    expect(renderer()->toHtml('> [!BANANA]'))->not->toContain('sl-alert');
});

it('renders tooltips', function (): void {
    $html = renderer()->toHtml('A ^[shadow root](Isolated DOM) keeps styles out.');

    expect($html)->toContain('class="sl-tip"')
        ->and($html)->toContain('data-sl-tip="Isolated DOM"')
        ->and($html)->toContain('>shadow root<');
});

it('escapes tooltip content', function (): void {
    expect(renderer()->toHtml('^[<b>hi</b>](<script>alert(1)</script>)'))
        ->not->toContain('<script>')
        ->not->toContain('<b>hi</b>');
});

it('renders images lazily', function (): void {
    $html = renderer()->toHtml('![A screenshot](https://img.test/a.png "Timeline")');

    expect($html)->toContain('loading="lazy"')
        ->and($html)->toContain('decoding="async"')
        ->and($html)->toContain('class="sl-img"')
        ->and($html)->toContain('alt="A screenshot"');
});

it('escapes raw html by default', function (): void {
    expect(renderer()->toHtml('<script>alert(1)</script>'))->not->toContain('<script>');
});

it('allows raw html when explicitly enabled', function (): void {
    expect(renderer(allowRawHtml: true)->toHtml('<span class="mine">hi</span>'))
        ->toContain('<span class="mine">hi</span>');
});

it('renders tables from the github flavoured extension', function (): void {
    expect(renderer()->toHtml("| a | b |\n| - | - |\n| 1 | 2 |"))->toContain('<table>');
});

it('strips the wrapping paragraph for inline fragments', function (): void {
    expect(renderer()->toInlineHtml('just *text*'))->toBe('just <em>text</em>');
});

it('keeps multi paragraph fragments intact', function (): void {
    expect(renderer()->toInlineHtml("one\n\ntwo"))->toContain('<p>one</p>');
});

it('returns an empty string for blank markdown', function (): void {
    expect(renderer()->toHtml(null))->toBe('')
        ->and(renderer()->toHtml('   '))->toBe('');
});
