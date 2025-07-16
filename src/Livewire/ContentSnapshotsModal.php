<?php

declare(strict_types=1);

namespace CmsMax\FilamentSnapshots\Livewire;

use CmsMax\FilamentSnapshots\Models\ContentSnapshot;
use CmsMax\FilamentSnapshots\Services\SnapshotService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ContentSnapshotsModal extends Component implements HasForms
{
    use InteractsWithForms;

    public Model $record;
    public $snapshots;
    public $selectedSnapshot = null;
    public $showDiff = false;
    public $heading = '';

    public function mount(Model $record): void
    {
        $this->record = $record;
        $this->loadSnapshots();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('heading')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter snapshot description'),
            ]);
    }

    public function loadSnapshots(): void
    {
        $this->snapshots = $this->record->getSnapshots(
            config('filament-snapshots.ui.snapshots_per_page', 20)
        );
    }

    public function createSnapshot(): void
    {
        $data = $this->form->getState();
        
        $snapshot = $this->record->createSnapshot($data['heading']);
        
        Notification::make()
            ->title('Snapshot Created')
            ->body("Snapshot '{$snapshot->heading}' has been created successfully.")
            ->success()
            ->send();
            
        $this->loadSnapshots();
        $this->form->fill();
    }

    public function deleteSnapshot(int $snapshotId): void
    {
        $snapshot = ContentSnapshot::find($snapshotId);
        
        if ($snapshot) {
            $snapshotHeading = $snapshot->heading;
            app(SnapshotService::class)->deleteSnapshot($snapshot);
            
            Notification::make()
                ->title('Snapshot Deleted')
                ->body("Snapshot '{$snapshotHeading}' has been deleted successfully.")
                ->success()
                ->send();
                
            $this->loadSnapshots();
        }
    }

    public function showRestore(int $snapshotId): void
    {
        $this->selectedSnapshot = ContentSnapshot::find($snapshotId);
        $this->showDiff = true;
    }

    public function restoreSnapshot(): void
    {
        if ($this->selectedSnapshot) {
            $this->selectedSnapshot->restore();
            
            Notification::make()
                ->title('Snapshot Restored')
                ->body("Content has been restored from snapshot '{$this->selectedSnapshot->heading}'.")
                ->success()
                ->send();
                
            $this->showDiff = false;
            $this->selectedSnapshot = null;
        }
    }

    public function cancelRestore(): void
    {
        $this->showDiff = false;
        $this->selectedSnapshot = null;
    }

    public function render()
    {
        return view('filament-snapshots::livewire.content-snapshots-modal');
    }
}