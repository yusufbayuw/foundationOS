<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Tables;

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

class PurchaseRequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('requested_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('requested_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('request_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_number'))
                    ->searchable(),
                TextColumn::make('request_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('required_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('required_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(\Modules\Core\Support\FilamentUi::field('priority'))
                    ->searchable(),
                TextColumn::make('total_items')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_items'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_estimated_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('approved_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_at'))
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\PurchaseRequisitionImporter::class),
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
