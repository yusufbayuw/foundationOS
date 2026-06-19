<?php

namespace Modules\School\Filament\Resources\Violations\Tables;

use App\Filament\Imports\ViolationImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class ViolationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('violationType.name')
                    ->label(FilamentUi::field('violationType.name'))
                    ->searchable(),
                TextColumn::make('reported_by')
                    ->label(FilamentUi::field('reported_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('handled_by')
                    ->label(FilamentUi::field('handled_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('severity')
                    ->label(FilamentUi::field('severity'))
                    ->searchable(),
                TextColumn::make('location')
                    ->label(FilamentUi::field('location'))
                    ->searchable(),
                TextColumn::make('sanction_duration_days')
                    ->label(FilamentUi::field('sanction_duration_days'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('parent_notified')
                    ->label(FilamentUi::field('parent_notified'))
                    ->boolean(),
                TextColumn::make('parent_meeting_date')
                    ->label(FilamentUi::field('parent_meeting_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ViolationImporter::class),
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
