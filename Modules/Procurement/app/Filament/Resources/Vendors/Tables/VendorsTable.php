<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Tables;

use App\Filament\Imports\VendorImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('province.name')
                    ->label(FilamentUi::field('province.name'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(FilamentUi::field('city.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('business_field')
                    ->label(FilamentUi::field('business_field'))
                    ->searchable(),
                TextColumn::make('npwp')
                    ->label(FilamentUi::field('npwp'))
                    ->searchable(),
                TextColumn::make('nib')
                    ->label(FilamentUi::field('nib'))
                    ->searchable(),
                TextColumn::make('siup')
                    ->label(FilamentUi::field('siup'))
                    ->searchable(),
                TextColumn::make('tdp')
                    ->label(FilamentUi::field('tdp'))
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->label(FilamentUi::field('postal_code'))
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
                TextColumn::make('contact_person')
                    ->label(FilamentUi::field('contact_person'))
                    ->searchable(),
                TextColumn::make('contact_position')
                    ->label(FilamentUi::field('contact_position'))
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label(FilamentUi::field('contact_phone'))
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->label(FilamentUi::field('contact_email'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('bank_account')
                    ->label(FilamentUi::field('bank_account'))
                    ->searchable(),
                TextColumn::make('bank_account_holder')
                    ->label(FilamentUi::field('bank_account_holder'))
                    ->searchable(),
                TextColumn::make('tax_status')
                    ->label(FilamentUi::field('tax_status'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_blacklisted')
                    ->label(FilamentUi::field('is_blacklisted'))
                    ->boolean(),
                TextColumn::make('performance_rating')
                    ->label(FilamentUi::field('performance_rating'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_transactions')
                    ->label(FilamentUi::field('total_transactions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_transaction_value')
                    ->label(FilamentUi::field('total_transaction_value'))
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
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                SelectFilter::make('is_blacklisted')
                    ->options([
                        '1' => 'Blacklisted',
                        '0' => 'Not Blacklisted',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(VendorImporter::class),
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
