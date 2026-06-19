<?php

namespace Modules\Core\Filament\Resources\Tenants\Tables;

use App\Filament\Imports\TenantImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label(FilamentUi::text('UUID'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('domain')
                    ->label(FilamentUi::field('domain'))
                    ->searchable(),
                TextColumn::make('subdomain')
                    ->label(FilamentUi::field('subdomain'))
                    ->searchable(),
                ImageColumn::make('logo')
                    ->label(FilamentUi::field('logo'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('favicon')
                    ->label(FilamentUi::field('favicon'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('primary_color')
                    ->label(FilamentUi::field('primary_color'))
                    ->searchable(),
                TextColumn::make('secondary_color')
                    ->label(FilamentUi::field('secondary_color'))
                    ->searchable(),
                TextColumn::make('timezone')
                    ->label(FilamentUi::field('timezone'))
                    ->searchable(),
                TextColumn::make('currency')
                    ->label(FilamentUi::field('currency'))
                    ->searchable(),
                TextColumn::make('locale')
                    ->label(FilamentUi::field('locale'))
                    ->searchable(),
                TextColumn::make('billing_cycle')
                    ->label(FilamentUi::field('billing_cycle'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('trial_ends_at')
                    ->label(FilamentUi::field('trial_ends_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscribed_at')
                    ->label(FilamentUi::field('subscribed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscription_expires_at')
                    ->label(FilamentUi::field('subscription_expires_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('max_users')
                    ->label(FilamentUi::field('max_users'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_organizations')
                    ->label(FilamentUi::field('max_organizations'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_storage_mb')
                    ->label(FilamentUi::field('max_storage_mb'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->label(FilamentUi::field('meta_title'))
                    ->searchable(),
                TextColumn::make('subscriptionPlan.name')
                    ->label(FilamentUi::field('subscriptionPlan.name'))
                    ->searchable(),
                TextColumn::make('created_by')
                    ->label(FilamentUi::field('created_by'))
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
                ...ImportTableActions::make(TenantImporter::class),
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
