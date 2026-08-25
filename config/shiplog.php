<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Feed Route
    |--------------------------------------------------------------------------
    |
    | The timeline fetches its releases as JSON from this route. Routes are
    | registered before any panel boots, so this cannot be set on the plugin.
    | Add your own middleware here to lock the feed down further.
    |
    */
    'route' => [
        'enabled' => true,
        'prefix' => 'shiplog',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gates
    |--------------------------------------------------------------------------
    |
    | The ability names checked before showing or editing the changelog. They
    | are only defined if your application has not already defined them, so a
    | policy or service provider of your own always wins.
    |
    | To change who passes rather than what the gates are called, use
    | ShipLogPlugin::make()->authorizeView() and ->authorizeManage().
    |
    */
    'gates' => [
        'view' => 'shiplog.view',
        'manage' => 'shiplog.manage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Releases Table
    |--------------------------------------------------------------------------
    |
    | Read by the migration, which runs outside of any panel.
    |
    */
    'table' => 'shiplog_releases',

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Everything else lives on the plugin:
    |
    |     ShipLogPlugin::make()
    |         ->usingMarkdown(base_path('CHANGELOG.md'))
    |         ->cache(true, ttl: 3600)
    |         ->perPage(20)
    |         ->fab(FabPosition::BottomRight)
    |
    | These two remain because an application can use the frontend timeline
    | without registering a panel at all.
    |
    */
    'driver' => env('SHIPLOG_DRIVER', 'markdown'),

    'markdown' => [
        'path' => env('SHIPLOG_PATH'),
    ],
];
