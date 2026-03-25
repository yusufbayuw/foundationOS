<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('province.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('province.name'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('city.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('business_field')
                    ->label(\Modules\Core\Support\FilamentUi::field('business_field'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('nib')
                    ->label(\Modules\Core\Support\FilamentUi::field('nib'))
                    ->searchable(),
                TextColumn::make('siup')
                    ->label(\Modules\Core\Support\FilamentUi::field('siup'))
                    ->searchable(),
                TextColumn::make('tdp')
                    ->label(\Modules\Core\Support\FilamentUi::field('tdp'))
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code'))
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
                TextColumn::make('contact_person')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_person'))
                    ->searchable(),
                TextColumn::make('contact_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_position'))
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_phone'))
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_email'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account'))
                    ->searchable(),
                TextColumn::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder'))
                    ->searchable(),
                TextColumn::make('tax_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_status'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_blacklisted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_blacklisted'))
                    ->boolean(),
                TextColumn::make('performance_rating')
                    ->label(\Modules\Core\Support\FilamentUi::field('performance_rating'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_transactions')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transactions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_transaction_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transaction_value'))
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
