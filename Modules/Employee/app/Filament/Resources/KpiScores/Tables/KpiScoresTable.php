<?php

namespace Modules\Employee\Filament\Resources\KpiScores\Tables;

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
use Modules\Employee\Enums\KpiScoreStatus;

class KpiScoresTable
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

                TextColumn::make('template.name')
                    ->label('Template KPI')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('period_month')
                    ->label('Bulan')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                        default => $state,
                    }),

                TextColumn::make('period_year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('total_score')
                    ->label('Total Skor')
                    ->numeric(2)
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (KpiScoreStatus $state): string => $state->getColor()),

                TextColumn::make('evaluator.name')
                    ->label('Evaluator')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('Disetujui')
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
                    ->options(KpiScoreStatus::class),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\KpiScoreImporter::class),
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
