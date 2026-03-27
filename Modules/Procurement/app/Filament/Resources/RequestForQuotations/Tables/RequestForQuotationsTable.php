<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class RequestForQuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchaseRequisition.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchaseRequisition.id'))
                    ->searchable(),
                TextColumn::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rfq_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_number'))
                    ->searchable(),
                TextColumn::make('rfq_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('closing_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('closing_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_estimated_budget')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_budget'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->searchable(),
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
                ...ImportTableActions::make(\App\Filament\Imports\RequestForQuotationImporter::class),
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
