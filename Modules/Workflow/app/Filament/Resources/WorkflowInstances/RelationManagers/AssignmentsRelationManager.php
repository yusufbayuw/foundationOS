<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assigned_to_id')
            ->columns([
                TextColumn::make('step.name')
                    ->label(FilamentUi::field('step_id'))
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('assigned_to_type')
                    ->label(FilamentUi::text('Assigned to type'))
                    ->badge(),
                TextColumn::make('assigned_to_id')
                    ->label(FilamentUi::text('Assigned user'))
                    ->formatStateUsing(fn (WorkflowAssignment $record): string => (string) data_get($record->meta, 'user_name', $record->assigned_to_id))
                    ->searchable(),
                TextColumn::make('assignment_role')
                    ->label(FilamentUi::text('Assignment role'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
                TextColumn::make('assigned_at')
                    ->label(FilamentUi::text('Assigned at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label(FilamentUi::field('due_at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('completed_at')
                    ->label(FilamentUi::text('Completed at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->headerActions([])
            ->recordActions([
                Action::make('reassign')
                    ->label(FilamentUi::text('Reassign'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (WorkflowAssignment $record): bool => $record->status->value === 'pending')
                    ->form([
                        Select::make('target_user_id')
                            ->label(FilamentUi::text('Target user'))
                            ->options(function (): array {
                                $instance = $this->getOwnerRecord();
                                assert($instance instanceof WorkflowInstance);

                                return User::query()
                                    ->whereHas('userTenantRoles', function ($query) use ($instance): void {
                                        $query->where('tenant_id', $instance->tenant_id);

                                        if ($instance->organization_id) {
                                            $query->where(function ($inner) use ($instance): void {
                                                $inner->where('organization_id', $instance->organization_id)
                                                    ->orWhereNull('organization_id');
                                            });
                                        }
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->required(),
                        Textarea::make('reason')
                            ->label(FilamentUi::field('reason'))
                            ->rows(3)
                            ->placeholder('-'),
                    ])
                    ->action(function (WorkflowAssignment $record, array $data): void {
                        /** @var User $actor */
                        $actor = auth()->user();
                        $targetUser = User::query()->findOrFail($data['target_user_id']);

                        app(WorkflowEngine::class)->reassign(
                            $record,
                            $actor,
                            $targetUser,
                            $data['reason'] ?? null,
                        );

                        Notification::make()
                            ->title(FilamentUi::text('Reassign'))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
}
