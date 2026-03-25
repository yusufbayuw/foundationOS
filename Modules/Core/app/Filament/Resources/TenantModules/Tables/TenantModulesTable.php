<?php

namespace Modules\Core\Filament\Resources\TenantModules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('module.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('module.name'))
                    ->searchable(),
                IconColumn::make('is_enabled')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_enabled'))
                    ->boolean(),
                TextColumn::make('enabled_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('enabled_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('disabled_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('disabled_at'))
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
