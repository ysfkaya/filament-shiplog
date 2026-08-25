<?php

use Ysfkaya\ShipLog\Markdown\ChangelogParser;
use Ysfkaya\ShipLog\Markdown\MarkdownRenderer;
use Ysfkaya\ShipLog\ShipLogManager;
use Ysfkaya\ShipLog\Tests\Fixtures\User;
use Ysfkaya\ShipLog\Tests\TestCase;

pest()->extend(TestCase::class)->in(__DIR__);

function parser(bool $allowRawHtml = false): ChangelogParser
{
    return new ChangelogParser(new MarkdownRenderer($allowRawHtml));
}

function renderer(bool $allowRawHtml = false): MarkdownRenderer
{
    return new MarkdownRenderer($allowRawHtml);
}

function changelogFixture(string $contents): string
{
    $path = sys_get_temp_dir() . '/shiplog-' . md5($contents) . '.md';

    file_put_contents($path, $contents);

    config()->set('shiplog.markdown.path', $path);

    return $path;
}

/**
 * A manager built from the current config, so a test can switch drivers
 * without fighting the container's cached instance.
 */
function shiplog(): ShipLogManager
{
    return new ShipLogManager(app());
}

function actingAsUser(): User
{
    $user = User::query()->create([
        'name' => 'Ada',
        'email' => 'ada@example.test',
        'password' => bcrypt('secret'),
    ]);

    test()->actingAs($user);

    return $user;
}
