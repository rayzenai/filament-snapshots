<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots\Services;

class DiffService
{
    public function generateDiff(string $original, string $new): array
    {
        $originalLines = $this->splitIntoLines($original);
        $newLines = $this->splitIntoLines($new);
        
        return $this->computeLineDiff($originalLines, $newLines);
    }

    private function splitIntoLines(string $text): array
    {
        if (empty($text)) {
            return [];
        }
        
        return explode("\n", $text);
    }

    private function computeLineDiff(array $originalLines, array $newLines): array
    {
        $diff = [];
        $originalIndex = 0;
        $newIndex = 0;
        
        while ($originalIndex < count($originalLines) || $newIndex < count($newLines)) {
            $originalLine = $originalLines[$originalIndex] ?? null;
            $newLine = $newLines[$newIndex] ?? null;
            
            if ($originalLine === null) {
                // Only new lines remain
                $diff[] = [
                    'type' => 'added',
                    'content' => $newLine,
                    'lineNumber' => $newIndex + 1,
                ];
                $newIndex++;
            } elseif ($newLine === null) {
                // Only original lines remain
                $diff[] = [
                    'type' => 'removed',
                    'content' => $originalLine,
                    'lineNumber' => $originalIndex + 1,
                ];
                $originalIndex++;
            } elseif ($originalLine === $newLine) {
                // Lines are identical
                $diff[] = [
                    'type' => 'unchanged',
                    'content' => $originalLine,
                    'lineNumber' => $originalIndex + 1,
                ];
                $originalIndex++;
                $newIndex++;
            } else {
                // Lines are different - show both
                $diff[] = [
                    'type' => 'removed',
                    'content' => $originalLine,
                    'lineNumber' => $originalIndex + 1,
                ];
                $diff[] = [
                    'type' => 'added',
                    'content' => $newLine,
                    'lineNumber' => $newIndex + 1,
                ];
                $originalIndex++;
                $newIndex++;
            }
        }
        
        return $this->addContextLines($diff);
    }

    private function addContextLines(array $diff): array
    {
        $result = [];
        $contextSize = 3;
        
        for ($i = 0; $i < count($diff); $i++) {
            $line = $diff[$i];
            
            // If this is a changed line, include context
            if ($line['type'] !== 'unchanged') {
                // Add context before
                $startContext = max(0, $i - $contextSize);
                for ($j = $startContext; $j < $i; $j++) {
                    if ($diff[$j]['type'] === 'unchanged' && !in_array($diff[$j], $result)) {
                        $result[] = $diff[$j];
                    }
                }
                
                // Add the changed line
                $result[] = $line;
                
                // Add context after
                $endContext = min(count($diff), $i + $contextSize + 1);
                for ($j = $i + 1; $j < $endContext; $j++) {
                    if ($diff[$j]['type'] === 'unchanged') {
                        $result[] = $diff[$j];
                    } else {
                        break; // Stop if we hit another change
                    }
                }
            }
        }
        
        return $result;
    }

    public function getInlineDiff(string $original, string $new): array
    {
        if ($original === $new) {
            return [
                'hasChanges' => false,
                'diff' => [],
            ];
        }

        $diff = $this->generateDiff($original, $new);
        
        return [
            'hasChanges' => true,
            'diff' => $diff,
        ];
    }

    public function formatDiffAsHtml(array $diff): string
    {
        $html = '';
        
        foreach ($diff as $line) {
            $escapedContent = htmlspecialchars($line['content']);
            $lineNumber = $line['lineNumber'];
            
            switch ($line['type']) {
                case 'added':
                    $html .= "<div class=\"diff-line diff-added\">";
                    $html .= "<span class=\"diff-line-number text-green-600\">+{$lineNumber}</span>";
                    $html .= "<span class=\"diff-content\">{$escapedContent}</span>";
                    $html .= "</div>";
                    break;
                    
                case 'removed':
                    $html .= "<div class=\"diff-line diff-removed\">";
                    $html .= "<span class=\"diff-line-number text-red-600\">-{$lineNumber}</span>";
                    $html .= "<span class=\"diff-content\">{$escapedContent}</span>";
                    $html .= "</div>";
                    break;
                    
                case 'unchanged':
                    $html .= "<div class=\"diff-line diff-unchanged\">";
                    $html .= "<span class=\"diff-line-number text-gray-400\"> {$lineNumber}</span>";
                    $html .= "<span class=\"diff-content\">{$escapedContent}</span>";
                    $html .= "</div>";
                    break;
            }
        }
        
        return $html;
    }

    /**
     * Generate diff for multiple fields
     */
    public function generateFieldsDiff(array $originalFields, array $newFields): array
    {
        $fieldDiffs = [];
        $allFields = array_unique(array_merge(array_keys($originalFields), array_keys($newFields)));
        
        foreach ($allFields as $fieldKey) {
            $originalValue = $originalFields[$fieldKey] ?? '';
            $newValue = $newFields[$fieldKey] ?? '';
            
            $fieldDiff = $this->getInlineDiff($originalValue, $newValue);
            
            if ($fieldDiff['hasChanges']) {
                $fieldDiffs[$fieldKey] = $fieldDiff;
            }
        }
        
        return $fieldDiffs;
    }

    /**
     * Check if snapshot differs from current model state
     */
    public function snapshotDiffersFromModel($snapshot, $model): bool
    {
        if (!empty($snapshot->field_data)) {
            $fieldMapping = $this->getFieldMapping($model);
            
            foreach ($snapshot->field_data as $fieldKey => $snapshotValue) {
                if (isset($fieldMapping[$fieldKey])) {
                    $modelAttribute = $fieldMapping[$fieldKey];
                    $modelValue = $model->{$modelAttribute} ?? '';
                    
                    if ($snapshotValue !== $modelValue) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Get field mapping for model (duplicated from SnapshotService to avoid circular dependency)
     */
    private function getFieldMapping($model): array
    {
        $modelClass = get_class($model);
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
     * Format field differences as HTML with tabbed interface
     */
    public function formatFieldDiffsAsHtml(array $fieldDiffs): string
    {
        if (empty($fieldDiffs)) {
            return '<p class="text-gray-500">No differences found.</p>';
        }

        $html = '<div class="field-diffs">';
        
        // Create tabs for each field
        $html .= '<div class="border-b border-gray-200">';
        $html .= '<nav class="-mb-px flex space-x-8">';
        
        $isFirst = true;
        foreach ($fieldDiffs as $fieldKey => $diff) {
            $fieldLabel = ucwords(str_replace(['_', '-'], ' ', $fieldKey));
            $activeClass = $isFirst ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
            
            $html .= "<button class=\"field-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {$activeClass}\" data-field=\"{$fieldKey}\">";
            $html .= $fieldLabel;
            $html .= '</button>';
            
            $isFirst = false;
        }
        
        $html .= '</nav>';
        $html .= '</div>';
        
        // Create content for each field
        $isFirst = true;
        foreach ($fieldDiffs as $fieldKey => $diff) {
            $displayClass = $isFirst ? 'block' : 'hidden';
            
            $html .= "<div class=\"field-content {$displayClass}\" data-field=\"{$fieldKey}\">";
            $html .= '<div class="mt-4">';
            $html .= $this->formatDiffAsHtml($diff['diff']);
            $html .= '</div>';
            $html .= '</div>';
            
            $isFirst = false;
        }
        
        $html .= '</div>';
        
        // Add JavaScript for tab switching
        $html .= '<script>';
        $html .= 'document.addEventListener("DOMContentLoaded", function() {';
        $html .= '  const tabs = document.querySelectorAll(".field-tab");';
        $html .= '  const contents = document.querySelectorAll(".field-content");';
        $html .= '  ';
        $html .= '  tabs.forEach(tab => {';
        $html .= '    tab.addEventListener("click", function() {';
        $html .= '      const fieldKey = this.dataset.field;';
        $html .= '      ';
        $html .= '      // Update tab styles';
        $html .= '      tabs.forEach(t => {';
        $html .= '        t.classList.remove("border-blue-500", "text-blue-600");';
        $html .= '        t.classList.add("border-transparent", "text-gray-500");';
        $html .= '      });';
        $html .= '      this.classList.remove("border-transparent", "text-gray-500");';
        $html .= '      this.classList.add("border-blue-500", "text-blue-600");';
        $html .= '      ';
        $html .= '      // Update content visibility';
        $html .= '      contents.forEach(content => {';
        $html .= '        content.classList.add("hidden");';
        $html .= '      });';
        $html .= '      document.querySelector(`[data-field="${fieldKey}"].field-content`).classList.remove("hidden");';
        $html .= '    });';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';
        
        return $html;
    }
}