<div>
    @if($showDiff && $selectedSnapshot)
        <!-- Diff View -->
        <div class="space-y-6">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600" />
                    <h3 class="font-medium text-amber-800">Restore Preview</h3>
                </div>
                <p class="text-sm text-amber-700 mt-2">
                    You are about to restore content from snapshot: <strong>{{ $selectedSnapshot->heading }}</strong>
                </p>
                <p class="text-xs text-amber-600 mt-1">
                    Created {{ $selectedSnapshot->created_at->diffForHumans() }}
                </p>
            </div>

            @php
                $htmlColumn = config('filament-snapshots.content_columns.html', 'html');
                $cssColumn = config('filament-snapshots.content_columns.css', 'css');
                $currentHtml = $record->{$htmlColumn} ?? '';
                $currentCss = $record->{$cssColumn} ?? '';
                $snapshotHtml = $selectedSnapshot->html ?? '';
                $snapshotCss = $selectedSnapshot->css ?? '';
                $hasChanges = ($currentHtml !== $snapshotHtml) || ($currentCss !== $snapshotCss);
            @endphp

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

            @if(!$hasChanges)
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

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button
                    type="button"
                    wire:click="cancelRestore"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="restoreSnapshot"
                    @if(!$hasChanges) disabled @endif
                    class="inline-flex items-center px-4 py-2 {{ $hasChanges ? 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500' : 'bg-gray-300 cursor-not-allowed' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                    Restore Snapshot
                </button>
            </div>
        </div>
    @else
        <!-- Snapshots List -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Available Snapshots</h3>
                <div class="flex items-center space-x-2">
                    <x-heroicon-o-camera class="w-5 h-5 text-gray-400" />
                    <span class="text-sm text-gray-500">{{ $snapshots->count() }} snapshots</span>
                </div>
            </div>

            @if($snapshots->isEmpty())
                <div class="text-center py-12">
                    <div class="flex flex-col items-center space-y-3">
                        <x-heroicon-o-camera class="w-16 h-16 text-gray-300" />
                        <h4 class="text-lg font-medium text-gray-900">No snapshots found</h4>
                        <p class="text-sm text-gray-500 max-w-sm">
                            Snapshots are automatically created when you make changes to content. 
                            You can also create manual snapshots for important versions.
                        </p>
                    </div>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($snapshots as $snapshot)
                        <div class="bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-medium text-gray-900">{{ $snapshot->heading }}</h4>
                                            @if($snapshot->metadata && isset($snapshot->metadata['auto_generated']) && $snapshot->metadata['auto_generated'])
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Auto
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Manual
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <p class="text-sm text-gray-500">
                                                {{ $snapshot->created_at->format('M j, Y g:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-400">
                                                {{ $snapshot->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if($snapshot->metadata && isset($snapshot->metadata['action']))
                                            <p class="text-xs text-gray-400 mt-1">
                                                Action: {{ ucfirst(str_replace('_', ' ', $snapshot->metadata['action'])) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button
                                            type="button"
                                            wire:click="showRestore({{ $snapshot->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-amber-100 border border-amber-300 rounded-md font-semibold text-xs text-amber-800 uppercase tracking-widest hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                                            Restore
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="deleteSnapshot({{ $snapshot->id }})"
                                            wire:confirm="Are you sure you want to delete this snapshot? This action cannot be undone."
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 border border-red-300 rounded-md font-semibold text-xs text-red-800 uppercase tracking-widest hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <x-heroicon-o-trash class="w-3 h-3 mr-1" />
                                            Delete
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    @if($snapshot->html)
                                        <div>
                                            <p class="text-xs font-medium text-gray-600 mb-2 flex items-center space-x-1">
                                                <x-heroicon-o-code-bracket class="w-3 h-3" />
                                                <span>HTML Content</span>
                                            </p>
                                            <div class="bg-gray-50 border rounded p-3 h-20 overflow-y-auto">
                                                <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ strip_tags($snapshot->html) }}</pre>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <p class="text-xs font-medium text-gray-400 mb-2 flex items-center space-x-1">
                                                <x-heroicon-o-code-bracket class="w-3 h-3" />
                                                <span>HTML Content</span>
                                            </p>
                                            <div class="bg-gray-100 border rounded p-3 h-20 flex items-center justify-center">
                                                <span class="text-xs text-gray-400">No content</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($snapshot->css)
                                        <div>
                                            <p class="text-xs font-medium text-gray-600 mb-2 flex items-center space-x-1">
                                                <x-heroicon-o-paint-brush class="w-3 h-3" />
                                                <span>CSS Styles</span>
                                            </p>
                                            <div class="bg-gray-50 border rounded p-3 h-20 overflow-y-auto">
                                                <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ $snapshot->css }}</pre>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <p class="text-xs font-medium text-gray-400 mb-2 flex items-center space-x-1">
                                                <x-heroicon-o-paint-brush class="w-3 h-3" />
                                                <span>CSS Styles</span>
                                            </p>
                                            <div class="bg-gray-100 border rounded p-3 h-20 flex items-center justify-center">
                                                <span class="text-xs text-gray-400">No styles</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($snapshots->count() >= config('filament-snapshots.ui.snapshots_per_page', 20))
                    <div class="text-center py-4 border-t">
                        <p class="text-sm text-gray-500">
                            Showing the {{ config('filament-snapshots.ui.snapshots_per_page', 20) }} most recent snapshots. Older snapshots are automatically cleaned up.
                        </p>
                    </div>
                @endif
            @endif

            <!-- Create Snapshot Form -->
            <div class="border-t pt-4">
                <form wire:submit="createSnapshot" class="flex space-x-3">
                    <div class="flex-1">
                        {{ $this->form }}
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                        Create Snapshot
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>