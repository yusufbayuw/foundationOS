<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Tables;

use App\Filament\Imports\TenantRoleImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class TenantRolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(FilamentUi::field('slug'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(FilamentUi::field('level'))
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label(FilamentUi::field('is_default'))
                    ->boolean(),
                IconColumn::make('is_super_admin')
                    ->label(FilamentUi::field('is_super_admin'))
                    ->boolean(),
                TextColumn::make('dashboard_route')
                    ->label(FilamentUi::field('dashboard_route'))
                    ->searchable(),
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
                ...ImportTableActions::make(TenantRoleImporter::class),
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
