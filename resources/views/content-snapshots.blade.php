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
                                {{ (\Rayzenai\FilamentSnapshots\Actions\ManageContentSnapshotsAction::restoreSnapshotAction())(['snapshot' => $snapshot]) }}
                                {{ (\Rayzenai\FilamentSnapshots\Actions\ManageContentSnapshotsAction::deleteSnapshotAction())(['snapshot' => $snapshot]) }}
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
</div>