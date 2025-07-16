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
                        
                        <div class="mt-4">
                            @php
                                $fieldData = $snapshot->field_data ?? [];
                                $hasFieldData = !empty($fieldData);
                                $fieldKeys = $hasFieldData ? array_keys($fieldData) : [];
                                $gridCols = count($fieldKeys) > 2 ? 'grid-cols-3' : 'grid-cols-2';
                            @endphp
                            
                            @if($hasFieldData)
                                <div class="grid {{ $gridCols }} gap-4">
                                    @foreach($fieldKeys as $fieldKey)
                                        @php
                                            $fieldValue = $fieldData[$fieldKey] ?? '';
                                            $fieldLabel = ucwords(str_replace(['_', '-'], ' ', $fieldKey));
                                            $icon = match($fieldKey) {
                                                'html', 'content', 'body' => 'heroicon-o-code-bracket',
                                                'css', 'styles' => 'heroicon-o-paint-brush',
                                                'title', 'heading' => 'heroicon-o-document-text',
                                                'meta_description', 'excerpt' => 'heroicon-o-document',
                                                default => 'heroicon-o-document-text'
                                            };
                                        @endphp
                                        
                                        @if(!empty($fieldValue))
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 mb-2 flex items-center space-x-1">
                                                    <x-dynamic-component :component="$icon" class="w-3 h-3" />
                                                    <span>{{ $fieldLabel }}</span>
                                                </p>
                                                <div class="bg-gray-50 border rounded p-3 h-20 overflow-y-auto">
                                                    <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ 
                                                        in_array($fieldKey, ['html', 'content', 'body']) 
                                                            ? strip_tags($fieldValue) 
                                                            : $fieldValue 
                                                    }}</pre>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <p class="text-xs font-medium text-gray-400 mb-2 flex items-center space-x-1">
                                                    <x-dynamic-component :component="$icon" class="w-3 h-3" />
                                                    <span>{{ $fieldLabel }}</span>
                                                </p>
                                                <div class="bg-gray-100 border rounded p-3 h-20 flex items-center justify-center">
                                                    <span class="text-xs text-gray-400">No content</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400">
                                        <x-heroicon-o-document class="w-8 h-8 mx-auto mb-2" />
                                        <p class="text-sm">No field data available</p>
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