<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Tables;

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

class UserTenantRolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('user.name'))
                    ->searchable(),
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('tenantRole.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenantRole.name'))
                    ->searchable(),
                TextColumn::make('assigned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('assigned_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('assigned_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_primary')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_primary'))
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
                ...ImportTableActions::make(\App\Filament\Imports\UserTenantRoleImporter::class),
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
