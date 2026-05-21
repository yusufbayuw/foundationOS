<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Tables;

use App\Filament\Imports\PurchaseRequisitionImporter;
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

class PurchaseRequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::field('user.name'))
                    ->searchable(),
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
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->searchable(),
                IconColumn::make('ready_for_sourcing')
                    ->label('Ready For Sourcing')
                    ->boolean(),
                TextColumn::make('approved_at')
                    ->label(FilamentUi::field('approved_at'))
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(PurchaseRequisitionImporter::class),
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
