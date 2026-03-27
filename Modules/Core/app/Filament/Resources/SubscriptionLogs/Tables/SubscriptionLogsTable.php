<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Tables;

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

class SubscriptionLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action'))
                    ->searchable(),
                TextColumn::make('previousPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('previousPlan.name'))
                    ->searchable(),
                TextColumn::make('newPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('newPlan.name'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('payment_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_proof'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_number'))
                    ->searchable(),
                TextColumn::make('invoice_url')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_url'))
                    ->searchable(),
                TextColumn::make('period_start')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_start'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('period_end')
                    ->label(\Modules\Core\Support\FilamentUi::field('period_end'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
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
                ...ImportTableActions::make(\App\Filament\Imports\SubscriptionLogImporter::class),
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
