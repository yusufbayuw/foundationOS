<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\Tables;

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

class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('price_monthly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_monthly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_yearly')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_yearly'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_storage_gb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_gb'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_recommended')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_recommended'))
                    ->boolean(),
                TextColumn::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
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
                ...ImportTableActions::make(\App\Filament\Imports\SubscriptionPlanImporter::class),
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
