<?php

namespace Modules\Core\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('short_name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->searchable(),
                TextColumn::make('npsn')
                    ->label(\Modules\Core\Support\FilamentUi::field('npsn'))
                    ->searchable(),
                TextColumn::make('nss')
                    ->label(\Modules\Core\Support\FilamentUi::field('nss'))
                    ->searchable(),
                TextColumn::make('accreditation_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('accreditation_status'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('website')
                    ->label(\Modules\Core\Support\FilamentUi::field('website'))
                    ->searchable(),
                TextColumn::make('province.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('province.name'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('city.name'))
                    ->searchable(),
                TextColumn::make('district.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('district.name'))
                    ->searchable(),
                TextColumn::make('village.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('village.name'))
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code'))
                    ->searchable(),
                TextColumn::make('latitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('latitude'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('longitude'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('established_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('established_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('principalUser.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('principalUser.name'))
                    ->searchable(),
                ImageColumn::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('stamp')
                    ->label(\Modules\Core\Support\FilamentUi::field('stamp'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('signature')
                    ->label(\Modules\Core\Support\FilamentUi::field('signature'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('letterhead')
                    ->label(\Modules\Core\Support\FilamentUi::field('letterhead'))
                    ->disk('public')
                    ->square(),
                IconColumn::make('is_main')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_main'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
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
                ...ImportTableActions::make(\App\Filament\Imports\OrganizationImporter::class),
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
