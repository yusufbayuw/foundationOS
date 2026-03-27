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
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Models\WorkflowAssignment;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assigned_to_id')
            ->columns([
                TextColumn::make('step.name')
                    ->label('Step')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('assigned_to_type')
                    ->label('Assigned To Type')
                    ->badge(),
                TextColumn::make('assigned_to_id')
                    ->label('Assigned User')
                    ->formatStateUsing(fn (WorkflowAssignment $record): string => (string) data_get($record->meta, 'user_name', $record->assigned_to_id))
                    ->searchable(),
                TextColumn::make('assignment_role')
                    ->label('Assignment Role')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('assigned_at')
                    ->label('Assigned At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label('Due At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('completed_at')
                    ->label('Completed At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->headerActions([])
            ->recordActions([
                Action::make('reassign')
                    ->label('Reassign')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (WorkflowAssignment $record): bool => $record->status->value === 'pending')
                    ->form([
                        Select::make('target_user_id')
                            ->label('Target User')
                            ->options(function (): array {
                                $instance = $this->getOwnerRecord();

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
                            ->label('Reason')
                            ->rows(3)
                            ->placeholder('Reason for reassignment.'),
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
                            ->title('Assignment berhasil dipindahkan.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
}
