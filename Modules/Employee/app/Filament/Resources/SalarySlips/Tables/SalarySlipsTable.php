<?php

namespace Modules\Employee\Filament\Resources\SalarySlips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Employee\Enums\SalarySlipStatus;

class SalarySlipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('period_year', 'desc')
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('period_label')
                    ->label('Periode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_earnings')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_deductions')
                    ->label('Total Potongan')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('net_salary')
                    ->label('Take-Home Pay')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('working_days')
                    ->label('Hari Kerja')
                    ->suffix(' hr')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (SalarySlipStatus $state): string => $state->getColor()),

                TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SalarySlipStatus::class),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\SalarySlipImporter::class),
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
