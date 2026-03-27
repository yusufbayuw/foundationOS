<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Tables;

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

class RfqVendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('requestForQuotation.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('requestForQuotation.id'))
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor.name'))
                    ->searchable(),
                TextColumn::make('invitation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('invitation_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('response_deadline')
                    ->label(\Modules\Core\Support\FilamentUi::field('response_deadline'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('responded_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('responded_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('quotation_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quotation_document')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_document'))
                    ->searchable(),
                TextColumn::make('technical_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('technical_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_shortlisted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_shortlisted'))
                    ->boolean(),
                IconColumn::make('is_awarded')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_awarded'))
                    ->boolean(),
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
                ...ImportTableActions::make(\App\Filament\Imports\RfqVendorImporter::class),
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
