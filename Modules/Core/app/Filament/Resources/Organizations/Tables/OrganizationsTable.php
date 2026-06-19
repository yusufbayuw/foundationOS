<?php

namespace Modules\Core\Filament\Resources\Organizations\Tables;

use App\Filament\Imports\OrganizationImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(FilamentUi::field('short_name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(FilamentUi::field('level'))
                    ->searchable(),
                TextColumn::make('npsn')
                    ->label(FilamentUi::field('npsn'))
                    ->searchable(),
                TextColumn::make('nss')
                    ->label(FilamentUi::field('nss'))
                    ->searchable(),
                TextColumn::make('accreditation_status')
                    ->label(FilamentUi::field('accreditation_status'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('website')
                    ->label(FilamentUi::field('website'))
                    ->searchable(),
                TextColumn::make('province.name')
                    ->label(FilamentUi::field('province.name'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(FilamentUi::field('city.name'))
                    ->searchable(),
                TextColumn::make('district.name')
                    ->label(FilamentUi::field('district.name'))
                    ->searchable(),
                TextColumn::make('village.name')
                    ->label(FilamentUi::field('village.name'))
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->label(FilamentUi::field('postal_code'))
                    ->searchable(),
                TextColumn::make('latitude')
                    ->label(FilamentUi::field('latitude'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->label(FilamentUi::field('longitude'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('established_date')
                    ->label(FilamentUi::field('established_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('principalUser.name')
                    ->label(FilamentUi::field('principalUser.name'))
                    ->searchable(),
                ImageColumn::make('logo')
                    ->label(FilamentUi::field('logo'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('stamp')
                    ->label(FilamentUi::field('stamp'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('signature')
                    ->label(FilamentUi::field('signature'))
                    ->disk('public')
                    ->square(),
                ImageColumn::make('letterhead')
                    ->label(FilamentUi::field('letterhead'))
                    ->disk('public')
                    ->square(),
                IconColumn::make('is_main')
                    ->label(FilamentUi::field('is_main'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
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
                ...ImportTableActions::make(OrganizationImporter::class),
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
