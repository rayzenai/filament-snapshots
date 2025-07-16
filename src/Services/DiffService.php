<?php

declare(strict_types=1);

namespace CmsMax\FilamentSnapshots\Services;

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
}