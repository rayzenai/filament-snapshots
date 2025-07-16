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
        'field_data',
        'metadata',
    ];

    protected $casts = [
        'field_data' => 'array',
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
        $updateData = [];
        
        if (!empty($this->field_data)) {
            $fieldMapping = $this->getFieldMapping();
            
            foreach ($this->field_data as $fieldKey => $fieldValue) {
                if (isset($fieldMapping[$fieldKey])) {
                    $updateData[$fieldMapping[$fieldKey]] = $fieldValue;
                }
            }
            
            if (!empty($updateData)) {
                $this->snapshotable->update($updateData);
            }
        }
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

    /**
     * Get field mapping for the snapshotable model
     */
    protected function getFieldMapping(): array
    {
        $modelClass = $this->snapshotable_type;
        $modelConfig = config("filament-snapshots.models.{$modelClass}.fields", []);
        
        // If no specific model config, use global field mapping
        if (empty($modelConfig)) {
            $modelConfig = config('filament-snapshots.default_fields', [
                'html' => 'html',
                'css' => 'css',
            ]);
        }
        
        return $modelConfig;
    }

    /**
     * Get field data value by key
     */
    public function getFieldValue(string $key): mixed
    {
        return $this->field_data[$key] ?? null;
    }

    /**
     * Set field data value by key
     */
    public function setFieldValue(string $key, mixed $value): void
    {
        $fieldData = $this->field_data ?? [];
        $fieldData[$key] = $value;
        $this->field_data = $fieldData;
    }

    /**
     * Get all field keys that have data
     */
    public function getFieldKeys(): array
    {
        return array_keys($this->field_data ?? []);
    }

    /**
     * Check if field has data
     */
    public function hasFieldData(string $key): bool
    {
        return isset($this->field_data[$key]) && !empty($this->field_data[$key]);
    }

    /**
     * Get HTML data from field_data (for convenience)
     */
    public function getHtmlAttribute(): ?string
    {
        return $this->field_data['html'] ?? null;
    }

    /**
     * Get CSS data from field_data (for convenience)
     */
    public function getCssAttribute(): ?string
    {
        return $this->field_data['css'] ?? null;
    }
}