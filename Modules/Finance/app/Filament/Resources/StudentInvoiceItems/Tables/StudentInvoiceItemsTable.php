<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Tables;

use App\Filament\Imports\StudentInvoiceItemImporter;
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

class StudentInvoiceItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studentInvoice.id')
                    ->label(FilamentUi::field('studentInvoice.id'))
                    ->searchable(),
                TextColumn::make('tuitionType.name')
                    ->label(FilamentUi::field('tuitionType.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(FilamentUi::field('quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label(FilamentUi::field('unit_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('penalty_amount')
                    ->label(FilamentUi::field('penalty_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label(FilamentUi::field('subtotal'))
                    ->numeric()
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
                ...ImportTableActions::make(StudentInvoiceItemImporter::class),
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
