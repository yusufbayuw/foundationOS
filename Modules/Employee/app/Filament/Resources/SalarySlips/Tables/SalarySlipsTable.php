<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Tables;

use App\Filament\Imports\SalarySlipImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class SalarySlipsTable
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
                TextColumn::make('period_month')
                    ->label(FilamentUi::field('period_month'))
                    ->searchable(),
                TextColumn::make('period_year')
                    ->label(FilamentUi::field('period_year'))
                    ->searchable(),
                TextColumn::make('period_label')
                    ->label(FilamentUi::field('period_label'))
                    ->searchable(),
                TextColumn::make('basic_salary')
                    ->label(FilamentUi::field('basic_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_earnings')
                    ->label(FilamentUi::field('total_earnings'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->label(FilamentUi::field('total_deductions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_salary')
                    ->label(FilamentUi::field('net_salary'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_days')
                    ->label(FilamentUi::field('working_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('working_hours')
                    ->label(FilamentUi::field('working_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->label(FilamentUi::field('overtime_hours'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leave_days')
                    ->label(FilamentUi::field('leave_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('absent_days')
                    ->label(FilamentUi::field('absent_days'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label(FilamentUi::field('paid_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('paid_via')
                    ->label(FilamentUi::field('paid_via'))
                    ->searchable(),
                IconColumn::make('is_sent')
                    ->label(FilamentUi::field('is_sent'))
                    ->boolean(),
                TextColumn::make('sent_at')
                    ->label(FilamentUi::field('sent_at'))
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
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(SalarySlipImporter::class),
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
