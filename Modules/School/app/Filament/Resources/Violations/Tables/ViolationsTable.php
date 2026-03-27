<?php

namespace Modules\School\Filament\Resources\Violations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class ViolationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student.id'))
                    ->searchable(),
                TextColumn::make('violationType.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('violationType.name'))
                    ->searchable(),
                TextColumn::make('reported_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('reported_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('handled_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('handled_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('severity')
                    ->label(\Modules\Core\Support\FilamentUi::field('severity'))
                    ->searchable(),
                TextColumn::make('location')
                    ->label(\Modules\Core\Support\FilamentUi::field('location'))
                    ->searchable(),
                TextColumn::make('sanction_duration_days')
                    ->label(\Modules\Core\Support\FilamentUi::field('sanction_duration_days'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('parent_notified')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_notified'))
                    ->boolean(),
                TextColumn::make('parent_meeting_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent_meeting_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\ViolationImporter::class),
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
