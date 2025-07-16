<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots\Actions;

use Rayzenai\FilamentSnapshots\Models\ContentSnapshot;
use Rayzenai\FilamentSnapshots\Services\SnapshotService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;

class ManageContentSnapshotsAction
{
    public static function make(): Action
    {
        return Action::make('manageSnapshots')
            ->label('Snapshots')
            ->icon('heroicon-o-camera')
            ->modalHeading('Content Snapshots')
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->modalContent(function (Model $record) {
                return view('filament-snapshots::livewire-wrapper', [
                    'record' => $record,
                ]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public static function makeGroup(): ActionGroup
    {
        return ActionGroup::make([
            self::createSnapshotAction(),
            self::viewSnapshotsAction(),
        ])
            ->label('Content Snapshots')
            ->icon('heroicon-o-camera')
            ->button()
            ->outlined();
    }

    private static function createSnapshotAction(): Action
    {
        return Action::make('createSnapshot')
            ->label('Create Snapshot')
            ->icon('heroicon-o-plus')
            ->form([
                TextInput::make('heading')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter snapshot description'),
            ])
            ->action(function (Model $record, array $data) {
                $snapshot = $record->createSnapshot($data['heading']);
                
                Notification::make()
                    ->title('Snapshot Created')
                    ->body("Snapshot '{$snapshot->heading}' has been created successfully.")
                    ->success()
                    ->send();
            });
    }

    private static function viewSnapshotsAction(): Action
    {
        return Action::make('viewSnapshots')
            ->label('View Snapshots')
            ->icon('heroicon-o-eye')
            ->modalHeading('Content Snapshots')
            ->modalWidth(MaxWidth::FourExtraLarge)
            ->modalContent(function (Model $record) {
                $snapshots = $record->getSnapshots(20);
                
                return view('filament-snapshots::content-snapshots', [
                    'snapshots' => $snapshots,
                    'record' => $record,
                ]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public static function restoreSnapshotAction(): Action
    {
        return Action::make('restoreSnapshot')
            ->label('Restore')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->modalHeading('Restore Content Snapshot')
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->modalContent(function (ContentSnapshot $snapshot) {
                return view('filament-snapshots::snapshot-diff', [
                    'snapshot' => $snapshot,
                    'record' => $snapshot->snapshotable,
                ]);
            })
            ->modalSubmitActionLabel('Restore Snapshot')
            ->action(function (ContentSnapshot $snapshot) {
                $snapshot->restore();
                
                Notification::make()
                    ->title('Snapshot Restored')
                    ->body("Content has been restored from snapshot '{$snapshot->heading}'.")
                    ->success()
                    ->send();
            });
    }

    public static function deleteSnapshotAction(): Action
    {
        return Action::make('deleteSnapshot')
            ->label('Delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Content Snapshot')
            ->modalDescription('Are you sure you want to delete this snapshot? This action cannot be undone.')
            ->action(function (ContentSnapshot $snapshot) {
                $snapshotHeading = $snapshot->heading;
                app(SnapshotService::class)->deleteSnapshot($snapshot);
                
                Notification::make()
                    ->title('Snapshot Deleted')
                    ->body("Snapshot '{$snapshotHeading}' has been deleted successfully.")
                    ->success()
                    ->send();
            });
    }
}