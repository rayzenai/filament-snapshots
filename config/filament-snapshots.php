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
        'content_snapshot' => \Rayzenai\FilamentSnapshots\Models\ContentSnapshot::class,
        
        // Model-specific field configurations
        // Example:
        // 'App\\Models\\Page' => [
        //     'fields' => [
        //         'content' => 'content',
        //         'meta_description' => 'meta_description',
        //         'title' => 'title',
        //     ],
        // ],
        // 'App\\Models\\BlogPost' => [
        //     'fields' => [
        //         'body' => 'body',
        //         'excerpt' => 'excerpt',
        //         'title' => 'title',
        //     ],
        // ],
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

    /*
    |--------------------------------------------------------------------------
    | Field Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which fields should be captured in snapshots. You can define
    | default fields that apply to all models, or specify fields per model.
    |
    */

    // Default fields for all models (used when no model-specific config exists)
    'default_fields' => [
        'html' => 'html',
        'css' => 'css',
    ],


    // Legacy content columns (deprecated - use default_fields instead)
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