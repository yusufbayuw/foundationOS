<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Tables;

use App\Filament\Imports\LeaveRequestImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('substituteEmployee.id')
                    ->label(FilamentUi::field('substituteEmployee.id'))
                    ->searchable(),
                TextColumn::make('supervisor.name')
                    ->label(FilamentUi::field('supervisor.name'))
                    ->searchable(),
                TextColumn::make('approver.name')
                    ->label(FilamentUi::field('approver.name'))
                    ->searchable(),
                TextColumn::make('leave_type')
                    ->label(FilamentUi::field('leave_type'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(FilamentUi::field('start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_days')
                    ->label(FilamentUi::field('total_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attachment')
                    ->label(FilamentUi::field('attachment'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('supervisor_approved_at')
                    ->label(FilamentUi::field('supervisor_approved_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(FilamentUi::field('approved_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('leave_type')
                    ->options([
                        'annual' => 'Annual',
                        'sick' => 'Sick',
                        'personal' => 'Personal',
                        'maternity' => 'Maternity',
                        'paternity' => 'Paternity',
                        'emergency' => 'Emergency',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(LeaveRequestImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
