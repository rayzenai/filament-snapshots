# Filament Snapshots

A Laravel package that provides content snapshots with restore functionality for Filament applications. This package allows you to create, manage, and restore content snapshots for models with HTML and CSS content.

## Features

- 📸 **Automatic snapshots** - Automatically create snapshots before content updates/deletions
- 🔧 **Manual snapshots** - Create snapshots with custom descriptions
- 🔄 **Easy restore** - Restore content from any snapshot with diff preview
- 🎨 **Filament integration** - Beautiful UI components for Filament admin panels
- ⚡ **Livewire powered** - Real-time updates and interactions
- 🗂️ **Polymorphic** - Works with any Eloquent model
- 🧹 **Auto-cleanup** - Automatically clean up old snapshots

## Installation

Install the package via Composer:

```bash
composer require rayzenai/filament-snapshots
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-snapshots-migrations"
php artisan migrate
```

Optionally, publish the config file:

```bash
php artisan vendor:publish --tag="filament-snapshots-config"
```

## Usage

### 1. Add the Trait to Your Model

Add the `HasContentSnapshots` trait to any model that has HTML/CSS content:

```php
use CmsMax\FilamentSnapshots\Concerns\HasContentSnapshots;

class Page extends Model
{
    use HasContentSnapshots;

    // Your model code...
}
```

### 2. Add the Action to Your Filament Resource

Add the snapshots action to your Filament resource header actions:

```php
use CmsMax\FilamentSnapshots\Actions\ManageContentSnapshotsAction;

class PageResource extends Resource
{
    // ... other resource methods

    public static function getHeaderActions(): array
    {
        return [
            ManageContentSnapshotsAction::make(),
            // ... other actions
        ];
    }
}
```

### 3. Using Snapshots

#### Automatic Snapshots

Snapshots are automatically created:

- Before updating content (when `html` or `css` fields change)
- Before deleting a model

#### Manual Snapshots

Create manual snapshots programmatically:

```php
$page = Page::find(1);
$snapshot = $page->createSnapshot('Before major redesign');
```

#### Restore from Snapshot

Restore content from a snapshot:

```php
$snapshot = ContentSnapshot::find(1);
$snapshot->restore();
```

#### Get Snapshots

Get all snapshots for a model:

```php
$snapshots = $page->getSnapshots();
```

## Configuration

The package comes with a configuration file that allows you to customize various aspects:

```php
return [
    'table_name' => 'content_snapshots',

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
```

## Customization

### Custom Column Names

If your model uses different column names for HTML/CSS content:

```php
// In your config/filament-snapshots.php
'content_columns' => [
    'html' => 'content',
    'css' => 'styles',
],
```

### Custom Views

Publish the views to customize the UI:

```bash
php artisan vendor:publish --tag="filament-snapshots-views"
```

## API

### Model Methods

```php
// Create a snapshot
$snapshot = $model->createSnapshot('Description');

// Get snapshots
$snapshots = $model->getSnapshots($limit);

// Restore from snapshot
$model->restoreFromSnapshot($snapshot);

// Delete all snapshots
$model->deleteSnapshots();
```

### Snapshot Methods

```php
// Restore snapshot
$snapshot->restore();

// Get the related model
$model = $snapshot->snapshotable;
```

## Requirements

- PHP 8.1+
- Laravel 10.0+
- Filament 3.0+
- Livewire 3.0+

## License

MIT License. Please see [License File](LICENSE.md) for more information.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Credits

- [CMS Max](https://cmsmax.com)
- [All Contributors](../../contributors)

## Support

For support, please contact us at [info@cmsmax.com](mailto:info@cmsmax.com) or create an issue on GitHub.
