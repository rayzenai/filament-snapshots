<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots\Services;

use Rayzenai\FilamentSnapshots\Models\ContentSnapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SnapshotService
{
    public function createSnapshot(Model $model, string $heading, array $metadata = []): ContentSnapshot
    {
        $htmlColumn = config('filament-snapshots.content_columns.html', 'html');
        $cssColumn = config('filament-snapshots.content_columns.css', 'css');
        
        // Add default metadata
        if (config('filament-snapshots.metadata.track_user', true)) {
            $metadata['user_id'] = auth()->id();
        }
        
        if (config('filament-snapshots.metadata.track_ip', false)) {
            $metadata['ip_address'] = request()->ip();
        }
        
        if (config('filament-snapshots.metadata.track_user_agent', false)) {
            $metadata['user_agent'] = request()->userAgent();
        }
        
        $snapshot = ContentSnapshot::create([
            'snapshotable_type' => get_class($model),
            'snapshotable_id' => $model->id,
            'heading' => $heading,
            'html' => $model->{$htmlColumn},
            'css' => $model->{$cssColumn},
            'metadata' => $metadata,
        ]);
        
        // Cleanup old snapshots if limit is exceeded
        $this->cleanupOldSnapshots($model);
        
        return $snapshot;
    }

    public function getSnapshots(Model $model, ?int $limit = null): Collection
    {
        $limit = $limit ?? config('filament-snapshots.ui.snapshots_per_page', 20);
        
        return ContentSnapshot::forModel(get_class($model), $model->id)
            ->recent($limit)
            ->get();
    }

    public function restoreSnapshot(ContentSnapshot $snapshot): void
    {
        $snapshot->restore();
    }

    public function deleteSnapshot(ContentSnapshot $snapshot): void
    {
        $snapshot->delete();
    }

    public function deleteSnapshotsForModel(Model $model): void
    {
        ContentSnapshot::forModel(get_class($model), $model->id)->delete();
    }

    public function autoSnapshot(Model $model, string $action = 'auto'): ?ContentSnapshot
    {
        if (!config('filament-snapshots.auto_snapshot.enabled', true)) {
            return null;
        }
        
        $htmlColumn = config('filament-snapshots.content_columns.html', 'html');
        $cssColumn = config('filament-snapshots.content_columns.css', 'css');
        
        if (empty($model->{$htmlColumn}) && empty($model->{$cssColumn})) {
            return null;
        }

        $heading = $this->generateAutoHeading($model, $action);

        return $this->createSnapshot($model, $heading, [
            'auto_generated' => true,
            'action' => $action,
        ]);
    }

    private function generateAutoHeading(Model $model, string $action): string
    {
        $modelName = class_basename($model);
        $timestamp = now()->format('M j, Y g:i A');

        return match ($action) {
            'before_update' => "Before update - {$timestamp}",
            'before_delete' => "Before deletion - {$timestamp}",
            'manual' => "Manual snapshot - {$timestamp}",
            default => "Auto snapshot - {$timestamp}",
        };
    }

    private function cleanupOldSnapshots(Model $model): void
    {
        $maxSnapshots = config('filament-snapshots.snapshot_limits.max_per_model', 50);
        $cleanupAfterDays = config('filament-snapshots.snapshot_limits.cleanup_after_days', 30);

        $query = ContentSnapshot::forModel(get_class($model), $model->id);

        // Delete snapshots older than configured days
        if ($cleanupAfterDays > 0) {
            $query->where('created_at', '<', now()->subDays($cleanupAfterDays))->delete();
        }

        // Keep only the most recent snapshots up to the limit
        $totalSnapshots = ContentSnapshot::forModel(get_class($model), $model->id)->count();
        if ($totalSnapshots > $maxSnapshots) {
            $snapshotsToDelete = $totalSnapshots - $maxSnapshots;
            
            ContentSnapshot::forModel(get_class($model), $model->id)
                ->oldest()
                ->limit($snapshotsToDelete)
                ->delete();
        }
    }
}