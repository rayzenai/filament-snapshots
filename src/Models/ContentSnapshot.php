<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentSnapshot extends Model
{
    protected $fillable = [
        'snapshotable_type',
        'snapshotable_id',
        'heading',
        'html',
        'css',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->table = config('filament-snapshots.table_name', 'content_snapshots');
    }

    public function snapshotable(): MorphTo
    {
        return $this->morphTo();
    }

    public function restore(): void
    {
        $htmlColumn = config('filament-snapshots.content_columns.html', 'html');
        $cssColumn = config('filament-snapshots.content_columns.css', 'css');
        
        $this->snapshotable->update([
            $htmlColumn => $this->html,
            $cssColumn => $this->css,
        ]);
    }

    public function scopeForModel($query, string $modelType, int $modelId)
    {
        return $query->where('snapshotable_type', $modelType)
                    ->where('snapshotable_id', $modelId);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest()->limit($limit);
    }
}