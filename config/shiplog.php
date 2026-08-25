<?php

use Ysfkaya\ShipLog\Enums\FabPosition;
use Ysfkaya\ShipLog\Models\Release;

return [

    /*
    |--------------------------------------------------------------------------
    | Changelog Driver
    |--------------------------------------------------------------------------
    |
    | Where releases come from. "markdown" reads a CHANGELOG.md file, while
    | "database" reads the releases table so they can be edited from the
    | Filament panel. Register your own with ShipLog::extend().
    |
    */
    'driver' => env('SHIPLOG_DRIVER', 'markdown'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Driver
    |--------------------------------------------------------------------------
    |
    | The changelog file to read, and whether raw HTML inside it should be
    | rendered. Every rich feature (alerts, tooltips, images) has a markdown
    | syntax, so leaving HTML escaped is safe.
    |
    */
    'markdown' => [
        'path' => env('SHIPLOG_PATH'),
        'allow_html' => env('SHIPLOG_ALLOW_HTML', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Driver
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | Page Size
    |--------------------------------------------------------------------------
    |
    | How many releases the timeline requests at a time. The rest load as the
    | reader scrolls.
    |
    */
    'per_page' => 15,

    'model' => Release::class,

    'table' => 'shiplog_releases',

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Parsing markdown on every request is wasteful once a changelog grows.
    | Cached entries are cleared automatically whenever a release is saved
    | through the panel.
    |
    */
    'cache' => [
        'enabled' => env('SHIPLOG_CACHE', false),
        'store' => env('SHIPLOG_CACHE_STORE'),
        'key' => 'shiplog.releases',
        'ttl' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Feed Route
    |--------------------------------------------------------------------------
    |
    | The timeline fetches its releases as JSON from this route. Add your own
    | middleware here if the changelog should only be reachable by signed in
    | users.
    |
    */
    'route' => [
        'enabled' => true,
        'prefix' => 'shiplog',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Floating Action Button
    |--------------------------------------------------------------------------
    |
    | Where the button sits, and which environments it appears in. An empty
    | "environments" list means every environment.
    |
    */
    'fab' => [
        'enabled' => true,
        'position' => FabPosition::BottomRight,
        'environments' => [],
        'label' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gates
    |--------------------------------------------------------------------------
    |
    | Gate names checked before showing or editing the changelog. They are
    | only defined if your application has not already defined them, so you
    | are free to own them in a policy or service provider.
    |
    */
    'gates' => [
        'view' => 'shiplog.view',
        'manage' => 'shiplog.manage',
    ],
];
