# Field Configuration Guide

This guide explains how to configure which fields to track in your content snapshots.

## Overview

The package uses a flexible JSON field storage system that allows you to:

- Configure which fields to capture per model
- Store any number of fields (not just HTML and CSS)
- Dynamically display all configured fields in the UI

## Getting Started

### 1. Install the Package

```bash
composer require rayzenai/filament-snapshots
```

### 2. Run Database Migrations

```bash
php artisan migrate
```

### 3. Configure Your Models

#### Option A: Use Default Fields (Recommended for most users)

No changes needed! The package will use the default fields (`html` and `css`) for basic content tracking.

#### Option B: Configure Model-Specific Fields

Update your `config/filament-snapshots.php` to define fields per model:

```php
return [
    // ... other config

    'models' => [
        'content_snapshot' => \Rayzenai\FilamentSnapshots\Models\ContentSnapshot::class,
        
        // Configure fields for specific models
        'App\\Models\\Page' => [
            'fields' => [
                'content' => 'content',           // field_key => model_attribute
                'meta_description' => 'meta_description',
                'title' => 'title',
            ],
        ],
        
        'App\\Models\\BlogPost' => [
            'fields' => [
                'body' => 'body',
                'excerpt' => 'excerpt',
                'title' => 'title',
                'meta_description' => 'seo_description',
            ],
        ],
    ],

    // Default fields for models without specific configuration
    'default_fields' => [
        'html' => 'html',
        'css' => 'css',
    ],
];
```

### 4. Verify Your Setup

#### Check Existing Snapshots

Your existing snapshots should continue to work. The package automatically handles both old and new formats.

#### Test New Snapshots

Create a new snapshot and verify it uses the JSON structure:

```php
$model = Page::find(1);
$snapshot = $model->createSnapshot('Test snapshot');

// Check the new structure
dd($snapshot->field_data); // Should contain your configured fields
```

#### Test Restore Functionality

```php
$snapshot = ContentSnapshot::find(1);
$snapshot->restore(); // Restores the tracked fields to the model
```

## Configuration Examples

### Simple Content Model

```php
'App\\Models\\Page' => [
    'fields' => [
        'content' => 'content',
        'title' => 'title',
    ],
],
```

### Complex Model with Multiple Fields

```php
'App\\Models\\Article' => [
    'fields' => [
        'title' => 'title',
        'body' => 'body',
        'excerpt' => 'excerpt',
        'meta_title' => 'seo_title',
        'meta_description' => 'seo_description',
        'custom_css' => 'custom_styles',
    ],
],
```

### E-commerce Product

```php
'App\\Models\\Product' => [
    'fields' => [
        'name' => 'name',
        'description' => 'description',
        'short_description' => 'short_description',
        'specifications' => 'technical_specs',
    ],
],
```

## New Features Available

### 1. Flexible Field Display

The UI automatically adapts to show all configured fields with appropriate icons and labels.

### 2. Advanced Diff View

Multi-field diffs with tabbed interface for easy comparison.

### 3. Configurable Auto-Snapshots

Auto-snapshots now trigger only when tracked fields change (not all model changes).

### 4. Better Performance

JSON storage is more efficient and allows for better indexing.

## API Methods

### ContentSnapshot Model Methods

```php
// Get specific field value
$snapshot->getFieldValue('title');

// Set specific field value
$snapshot->setFieldValue('title', 'New Title');

// Get all field keys that have data
$snapshot->getFieldKeys();

// Check if specific field has data
$snapshot->hasFieldData('content');

// Convenience accessors (if html/css fields are configured)
$snapshot->html; // Gets field_data['html']
$snapshot->css;  // Gets field_data['css']
```

### SnapshotService Methods

```php
$service = app(\Rayzenai\FilamentSnapshots\Services\SnapshotService::class);

// Check if any tracked fields have changed
$service->hasTrackedFieldChanges($model);
```

## Troubleshooting

### Issue: Migration Fails

**Solution:** Ensure you have the latest package version and check for conflicting migrations.

```bash
composer show rayzenai/filament-snapshots
php artisan migrate:status
```

### Issue: Snapshots Are Empty

**Solution:** Check your field configuration. Ensure the model attributes match your database columns.

```php
// Debug field mapping
$model = YourModel::find(1);
$service = app(\Rayzenai\FilamentSnapshots\Services\SnapshotService::class);
dd($service->hasTrackedFieldChanges($model));
```

### Issue: Auto-Snapshots Not Created

**Solution:** Verify your field configuration and check if the model attributes are actually changing.

```php
// Check what fields are being tracked
$fieldMapping = config("filament-snapshots.models.".get_class($model).".fields", []);
dd($fieldMapping);
```

## Performance Considerations

### Database Storage

- JSON columns are more storage-efficient for multiple fields
- Better indexing capabilities with modern databases
- Supports PostgreSQL JSONB for optimal performance

### Query Performance

```php
// Efficient querying of JSON data
ContentSnapshot::whereJsonContains('field_data->title', 'Some Title')->get();

// PostgreSQL JSONB operators (if using PostgreSQL)
ContentSnapshot::whereRaw("field_data->>'title' ILIKE ?", ['%search%'])->get();
```

## Getting Help

If you encounter issues:

1. Check the [GitHub Issues](https://github.com/rayzenai/filament-snapshots/issues)
2. Review your configuration against the examples above
3. Enable debug mode to see detailed error messages
4. Contact support at [info@rayzenai.com](mailto:info@rayzenai.com)

## Package Benefits

✅ **Flexible field configuration** - Track any model attributes  
✅ **Better performance** - More efficient storage and querying  
✅ **Enhanced UI** - Dynamic field display with proper labeling  
✅ **Advanced diffs** - Multi-field comparison with tabbed interface  
✅ **Future-proof** - Easily add new fields without schema changes  
✅ **Modern storage** - JSON/JSONB support for complex data

---

**Need more help?** Contact us at [info@rayzenai.com](mailto:info@rayzenai.com) or create an issue on [GitHub](https://github.com/rayzenai/filament-snapshots/issues).