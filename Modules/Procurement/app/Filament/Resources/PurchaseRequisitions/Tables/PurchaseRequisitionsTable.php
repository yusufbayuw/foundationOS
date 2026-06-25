<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Tables;

use App\Filament\Imports\PurchaseRequisitionImporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Filament\Support\Tables\CommonTableColumns;
use Modules\Core\Filament\Support\Tables\StandardSoftDeleteTable;
use Modules\Core\Filament\Support\Tables\StatusSelectFilter;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\Support\PurchaseRequisitionStatusOptions;

class PurchaseRequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return StandardSoftDeleteTable::applySoftDeleteDefaults(
            $table
                ->columns([
                    CommonTableColumns::tenantName(),
                    CommonTableColumns::userName(),
                    TextColumn::make('requested_by')
                        ->label(FilamentUi::field('requested_by'))
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('approved_by')
                        ->label(FilamentUi::field('approved_by'))
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('request_number')
                        ->label(FilamentUi::field('request_number'))
                        ->searchable(),
                    TextColumn::make('request_date')
                        ->label(FilamentUi::field('request_date'))
                        ->date()
                        ->sortable(),
                    TextColumn::make('required_date')
                        ->label(FilamentUi::field('required_date'))
                        ->date()
                        ->sortable(),
                    TextColumn::make('priority')
                        ->label(FilamentUi::field('priority'))
                        ->searchable(),
                    TextColumn::make('total_items')
                        ->label(FilamentUi::field('total_items'))
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('total_estimated_amount')
                        ->label(FilamentUi::field('total_estimated_amount'))
                        ->numeric()
                        ->sortable(),
                    CommonTableColumns::statusBadge(),
                    CommonTableColumns::booleanIcon('ready_for_sourcing', 'Ready For Sourcing'),
                    TextColumn::make('approved_at')
                        ->label(FilamentUi::field('approved_at'))
                        ->dateTime()
                        ->sortable(),
                    CommonTableColumns::createdAt(),
                    CommonTableColumns::updatedAt(),
                ])
                ->filters([
                    ...StandardSoftDeleteTable::filters(),
                    StatusSelectFilter::make(PurchaseRequisitionStatusOptions::filterLabels()),
                ])
                ->headerActions([
                    ...ImportTableActions::make(PurchaseRequisitionImporter::class),
                ]),
        );
    }
}
