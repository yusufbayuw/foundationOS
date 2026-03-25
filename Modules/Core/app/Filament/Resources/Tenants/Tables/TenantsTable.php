<?php

namespace Modules\Core\Filament\Resources\Tenants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label(\Modules\Core\Support\FilamentUi::text('UUID'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('domain')
                    ->label(\Modules\Core\Support\FilamentUi::field('domain'))
                    ->searchable(),
                TextColumn::make('subdomain')
                    ->label(\Modules\Core\Support\FilamentUi::field('subdomain'))
                    ->searchable(),
                TextColumn::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo'))
                    ->searchable(),
                TextColumn::make('favicon')
                    ->label(\Modules\Core\Support\FilamentUi::field('favicon'))
                    ->searchable(),
                TextColumn::make('primary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('primary_color'))
                    ->searchable(),
                TextColumn::make('secondary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('secondary_color'))
                    ->searchable(),
                TextColumn::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone'))
                    ->searchable(),
                TextColumn::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale'))
                    ->searchable(),
                TextColumn::make('billing_cycle')
                    ->label(\Modules\Core\Support\FilamentUi::field('billing_cycle'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('trial_ends_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('trial_ends_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscribed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscribed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscription_expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscription_expires_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_storage_mb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_mb'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->label(\Modules\Core\Support\FilamentUi::field('meta_title'))
                    ->searchable(),
                TextColumn::make('subscriptionPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscriptionPlan.name'))
                    ->searchable(),
                TextColumn::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
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
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
