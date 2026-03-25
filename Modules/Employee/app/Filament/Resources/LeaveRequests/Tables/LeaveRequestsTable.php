<?php

namespace Modules\Employee\Filament\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('employee.id'))
                    ->searchable(),
                TextColumn::make('substituteEmployee.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('substituteEmployee.id'))
                    ->searchable(),
                TextColumn::make('supervisor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('supervisor.name'))
                    ->searchable(),
                TextColumn::make('approver.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('approver.name'))
                    ->searchable(),
                TextColumn::make('leave_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('leave_type'))
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('attachment'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('supervisor_approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('supervisor_approved_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
