<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Tables;

use App\Filament\Imports\RequestForQuotationImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class RequestForQuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('purchaseRequisition.id')
                    ->label(FilamentUi::field('purchaseRequisition.id'))
                    ->searchable(),
                TextColumn::make('created_by')
                    ->label(FilamentUi::field('created_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rfq_number')
                    ->label(FilamentUi::field('rfq_number'))
                    ->searchable(),
                TextColumn::make('rfq_date')
                    ->label(FilamentUi::field('rfq_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('closing_date')
                    ->label(FilamentUi::field('closing_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('total_estimated_budget')
                    ->label(FilamentUi::field('total_estimated_budget'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(FilamentUi::field('currency'))
                    ->searchable(),
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
                ...ImportTableActions::make(RequestForQuotationImporter::class),
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
