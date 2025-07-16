<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots\Concerns;

use Rayzenai\FilamentSnapshots\Models\ContentSnapshot;
use Rayzenai\FilamentSnapshots\Services\SnapshotService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasContentSnapshots
{
    protected static function bootHasContentSnapshots(): void
    {
        static::updating(function ($model) {
            if (!config('filament-snapshots.auto_snapshot.on_update', true)) {
                return;
            }
            
            $snapshotService = app(SnapshotService::class);
            
            // Check if any tracked fields have changed
            if ($snapshotService->hasTrackedFieldChanges($model)) {
                $snapshotService->autoSnapshot($model, 'before_update');
            }
        });

        static::deleting(function ($model) {
            if (!config('filament-snapshots.auto_snapshot.on_delete', true)) {
                return;
            }
            
            app(SnapshotService::class)->autoSnapshot($model, 'before_delete');
        });
    }

    public function contentSnapshots(): MorphMany
    {
        return $this->morphMany(ContentSnapshot::class, 'snapshotable')
            ->latest();
    }

    public function createSnapshot(string $heading, array $metadata = []): ContentSnapshot
    {
        return app(SnapshotService::class)->createSnapshot($this, $heading, $metadata);
    }

    public function getSnapshots(?int $limit = null): Collection
    {
        return app(SnapshotService::class)->getSnapshots($this, $limit);
    }

    public function restoreFromSnapshot(ContentSnapshot $snapshot): void
    {
        app(SnapshotService::class)->restoreSnapshot($snapshot);
    }

    public function deleteSnapshots(): void
    {
        app(SnapshotService::class)->deleteSnapshotsForModel($this);
    }
}