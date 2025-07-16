@php
    $htmlColumn = config('filament-snapshots.content_columns.html', 'html');
    $cssColumn = config('filament-snapshots.content_columns.css', 'css');
    $currentHtml = $record->{$htmlColumn} ?? '';
    $currentCss = $record->{$cssColumn} ?? '';
    $snapshotHtml = $snapshot->html ?? '';
    $snapshotCss = $snapshot->css ?? '';
@endphp

<div class="space-y-6">
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-center space-x-2">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
            <h3 class="font-medium text-amber-800">Restore Preview</h3>
        </div>
        <p class="text-sm text-amber-700 mt-2">
            You are about to restore content from snapshot: <strong>{{ $snapshot->heading }}</strong>
        </p>
        <p class="text-xs text-amber-600 mt-1">
            Created {{ $snapshot->created_at->diffForHumans() }}
        </p>
    </div>

    @if($currentHtml !== $snapshotHtml)
        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 flex items-center space-x-2">
                <x-heroicon-o-code-bracket class="w-4 h-4" />
                <span>HTML Changes</span>
            </h4>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center space-x-1">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span>Current Content</span>
                    </h5>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 {{ config('filament-snapshots.ui.diff_height', 'h-64') }} overflow-y-auto">
                        <pre class="text-xs text-red-900 whitespace-pre-wrap">{{ $currentHtml ?: 'No content' }}</pre>
                    </div>
                </div>
                
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center space-x-1">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span>Snapshot Content</span>
                    </h5>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 {{ config('filament-snapshots.ui.diff_height', 'h-64') }} overflow-y-auto">
                        <pre class="text-xs text-green-900 whitespace-pre-wrap">{{ $snapshotHtml ?: 'No content' }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600" />
                <h4 class="font-medium text-gray-900">HTML Content</h4>
            </div>
            <p class="text-sm text-gray-600 mt-1">No changes detected in HTML content.</p>
        </div>
    @endif

    @if($currentCss !== $snapshotCss)
        <div class="space-y-3">
            <h4 class="font-medium text-gray-900 flex items-center space-x-2">
                <x-heroicon-o-paint-brush class="w-4 h-4" />
                <span>CSS Changes</span>
            </h4>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center space-x-1">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span>Current Styles</span>
                    </h5>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 {{ config('filament-snapshots.ui.diff_height', 'h-64') }} overflow-y-auto">
                        <pre class="text-xs text-red-900 whitespace-pre-wrap">{{ $currentCss ?: 'No styles' }}</pre>
                    </div>
                </div>
                
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center space-x-1">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span>Snapshot Styles</span>
                    </h5>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 {{ config('filament-snapshots.ui.diff_height', 'h-64') }} overflow-y-auto">
                        <pre class="text-xs text-green-900 whitespace-pre-wrap">{{ $snapshotCss ?: 'No styles' }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600" />
                <h4 class="font-medium text-gray-900">CSS Content</h4>
            </div>
            <p class="text-sm text-gray-600 mt-1">No changes detected in CSS content.</p>
        </div>
    @endif

    @if($currentHtml === $snapshotHtml && $currentCss === $snapshotCss)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600" />
                <h4 class="font-medium text-blue-900">No Changes</h4>
            </div>
            <p class="text-sm text-blue-700 mt-1">
                The current content is identical to this snapshot. No changes will be made.
            </p>
        </div>
    @endif
</div>