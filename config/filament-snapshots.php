<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Snapshot Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how snapshots are handled in your application.
    |
    */

    'table_name' => 'content_snapshots',

    'models' => [
        'content_snapshot' => \CmsMax\FilamentSnapshots\Models\ContentSnapshot::class,
    ],

    'auto_snapshot' => [
        'enabled' => true,
        'on_update' => true,
        'on_delete' => true,
    ],

    'snapshot_limits' => [
        'max_per_model' => 50,
        'cleanup_after_days' => 30,
    ],

    'ui' => [
        'modal_width' => 'seven_extra_large',
        'snapshots_per_page' => 20,
        'diff_height' => 'h-64',
    ],

    'content_columns' => [
        'html' => 'html',
        'css' => 'css',
    ],

    'metadata' => [
        'track_user' => true,
        'track_ip' => false,
        'track_user_agent' => false,
    ],
];