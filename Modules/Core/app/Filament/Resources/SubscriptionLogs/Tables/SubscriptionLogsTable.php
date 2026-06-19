<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Tables;

use App\Filament\Imports\SubscriptionLogImporter;
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

class SubscriptionLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('action')
                    ->label(FilamentUi::field('action'))
                    ->searchable(),
                TextColumn::make('previousPlan.name')
                    ->label(FilamentUi::field('previousPlan.name'))
                    ->searchable(),
                TextColumn::make('newPlan.name')
                    ->label(FilamentUi::field('newPlan.name'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->label(FilamentUi::field('payment_method'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('payment_proof')
                    ->label(FilamentUi::field('payment_proof'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(FilamentUi::field('invoice_number'))
                    ->searchable(),
                TextColumn::make('invoice_url')
                    ->label(FilamentUi::field('invoice_url'))
                    ->searchable(),
                TextColumn::make('period_start')
                    ->label(FilamentUi::field('period_start'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('period_end')
                    ->label(FilamentUi::field('period_end'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_by')
                    ->label(FilamentUi::field('processed_by'))
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
                ...ImportTableActions::make(SubscriptionLogImporter::class),
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
