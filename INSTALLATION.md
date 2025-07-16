# Installation Guide for Existing CMS Max V3 App

This guide shows how to integrate the `filament-snapshots` package into your existing CMS Max V3 application.

## Steps

### 1. Add Package to Main Composer
Add the package to your main `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/filament-snapshots"
        }
    ],
    "require": {
        "cmsmax/filament-snapshots": "*"
    }
}
```

### 2. Update AppServiceProvider
Remove the ContentSnapshot from the morph map since it's now handled by the package:

```php
// In app/Providers/AppServiceProvider.php
// Remove this line:
// 'content_snapshot' => ContentSnapshot::class,
```

### 3. Update Models
Replace the existing trait usage with the package trait:

```php
// In app/Models/Page.php, Blog.php, Category.php, Brand.php
// Replace:
use App\Models\Concerns\HasContentSnapshots;

// With:
use CmsMax\FilamentSnapshots\Concerns\HasContentSnapshots;
```

### 4. Update Filament Actions
Replace the existing action with the package action:

```php
// In your Filament resources
// Replace:
use App\Filament\Actions\ManageContentSnapshotsAction;

// With:
use CmsMax\FilamentSnapshots\Actions\ManageContentSnapshotsAction;
```

### 5. Remove Old Files
Delete the old files since they're now in the package:

```bash
# Models
rm app/Models/ContentSnapshot.php

# Services
rm app/Services/SnapshotService.php
rm app/Services/DiffService.php

# Concerns
rm app/Models/Concerns/HasContentSnapshots.php

# Actions
rm app/Filament/Actions/ManageContentSnapshotsAction.php

# Livewire
rm app/Livewire/ContentSnapshotsModal.php

# Views
rm resources/views/livewire/content-snapshots-modal.blade.php
rm resources/views/filament/actions/content-snapshots.blade.php
rm resources/views/filament/actions/snapshot-diff.blade.php
rm resources/views/filament/actions/livewire-wrapper.blade.php

# Migration (keep the existing one or run the new one)
# rm database/migrations/tenant/2025_07_16_000000_create_content_snapshots_table.php
```

### 6. Install Package
Run composer install to install the package:

```bash
composer install
```

### 7. Run Migrations (if needed)
If you haven't run the content_snapshots migration yet:

```bash
php artisan migrate
```

### 8. Clear Cache
Clear all caches:

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Configuration

The package will use the default configuration. If you want to customize it, publish the config:

```bash
php artisan vendor:publish --tag="filament-snapshots-config"
```

## Verification

After installation, verify that:

1. The snapshots button appears in your resource header actions
2. Creating snapshots works
3. Restoring snapshots works
4. Auto-snapshots are created on content changes

## Benefits of Package

- ✅ Reusable across multiple projects
- ✅ Separate versioning and updates
- ✅ Cleaner main application code
- ✅ Easy to distribute and share
- ✅ Configurable and customizable
- ✅ Proper namespace separation