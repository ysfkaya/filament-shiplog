<?php

return [

    'navigation' => [
        'label' => 'Changelog',
        'title' => 'Changelog',
    ],

    'timeline' => [
        'label' => 'What\'s new',
        'heading' => 'What\'s new',
        'subheading' => 'Everything we shipped, newest first.',
        'empty' => 'Nothing shipped yet.',
        'error' => 'The changelog could not be loaded.',
    ],

    'actions' => [
        'manage' => 'Manage releases',
        'flush' => 'Clear cache',
        'flushed' => 'Changelog cache cleared.',
    ],

    'resource' => [
        'label' => 'Release',
        'plural_label' => 'Releases',
    ],

    'form' => [
        'details' => 'Release details',
        'version' => 'Version',
        'title' => 'Headline',
        'title_placeholder' => 'Dark mode, faster search',
        'released_at' => 'Release date',
        'released_at_hint' => 'Leave empty to publish it as unreleased. A future date keeps it hidden until then.',
        'status' => 'Status',
        'environments' => 'Environments',
        'environments_hint' => 'Leave empty to show this release everywhere.',
        'yanked' => 'Yanked',
        'yanked_hint' => 'Mark a release that was pulled after shipping.',
        'body' => 'Release notes',
        'body_hint' => 'Markdown, plus > [!WARNING] callouts and ^[tooltips](like this one).',
    ],

    'table' => [
        'unreleased' => 'Unreleased',
        'all_environments' => 'All',
    ],

    'status' => [
        'draft' => 'Draft',
        'published' => 'Published',
    ],

    'change_type' => [
        'added' => 'Added',
        'changed' => 'Changed',
        'deprecated' => 'Deprecated',
        'removed' => 'Removed',
        'fixed' => 'Fixed',
        'security' => 'Security',
    ],

    'fab_position' => [
        'top-left' => 'Top left',
        'top-right' => 'Top right',
        'bottom-left' => 'Bottom left',
        'bottom-right' => 'Bottom right',
    ],
];
