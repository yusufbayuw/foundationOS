<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Tables;

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

class StudentInvoiceItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studentInvoice.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('studentInvoice.id'))
                    ->searchable(),
                TextColumn::make('tuitionType.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tuitionType.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('penalty_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('penalty_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->numeric()
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
                ...ImportTableActions::make(\App\Filament\Imports\StudentInvoiceItemImporter::class),
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
