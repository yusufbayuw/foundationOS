<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalarySlipsTable
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
                TextColumn::make('period_month')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_month'))
                    ->searchable(),
                TextColumn::make('period_year')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_year'))
                    ->searchable(),
                TextColumn::make('period_label')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_label'))
                    ->searchable(),
                TextColumn::make('basic_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('basic_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_earnings')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_earnings'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_deductions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_salary')
                    ->label(\Modules\Core\Support\FilamentUi::field('net_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('working_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('working_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leave_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('leave_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('absent_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('absent_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('paid_via')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_via'))
                    ->searchable(),
                IconColumn::make('is_sent')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_sent'))
                    ->boolean(),
                TextColumn::make('sent_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
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
