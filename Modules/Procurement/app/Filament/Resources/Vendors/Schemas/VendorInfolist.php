<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type')),
                        TextEntry::make('business_field')
                            ->label(FilamentUi::field('business_field'))
                            ->placeholder('-'),
                        TextEntry::make('tax_status')
                            ->label(FilamentUi::field('tax_status'))
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                        IconEntry::make('is_blacklisted')
                            ->boolean(),
                        TextEntry::make('blacklist_reason')
                            ->label(FilamentUi::field('blacklist_reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Legal Documents'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('npwp')
                            ->label(FilamentUi::field('npwp'))
                            ->placeholder('-'),
                        TextEntry::make('nib')
                            ->label(FilamentUi::field('nib'))
                            ->placeholder('-'),
                        TextEntry::make('siup')
                            ->label(FilamentUi::field('siup'))
                            ->placeholder('-'),
                        TextEntry::make('tdp')
                            ->label(FilamentUi::field('tdp'))
                            ->placeholder('-'),
                        TextEntry::make('documents')
                            ->label(FilamentUi::field('documents'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Address & Contact'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('province.name')
                            ->label(FilamentUi::text('Province'))
                            ->placeholder('-'),
                        TextEntry::make('city.name')
                            ->label(FilamentUi::text('City'))
                            ->placeholder('-'),
                        TextEntry::make('postal_code')
                            ->label(FilamentUi::field('postal_code'))
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('website')
                            ->label(FilamentUi::field('website'))
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label(FilamentUi::field('address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Contact Person'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('contact_person')
                            ->label(FilamentUi::field('contact_person'))
                            ->placeholder('-'),
                        TextEntry::make('contact_position')
                            ->label(FilamentUi::field('contact_position'))
                            ->placeholder('-'),
                        TextEntry::make('contact_phone')
                            ->label(FilamentUi::field('contact_phone'))
                            ->placeholder('-'),
                        TextEntry::make('contact_email')
                            ->label(FilamentUi::field('contact_email'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Banking'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bank_name')
                            ->label(FilamentUi::field('bank_name'))
                            ->placeholder('-'),
                        TextEntry::make('bank_account')
                            ->label(FilamentUi::field('bank_account'))
                            ->placeholder('-'),
                        TextEntry::make('bank_account_holder')
                            ->label(FilamentUi::field('bank_account_holder'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Performance'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('performance_rating')
                            ->label(FilamentUi::field('performance_rating'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('total_transactions')
                            ->label(FilamentUi::field('total_transactions'))
                            ->numeric(),
                        TextEntry::make('total_transaction_value')
                            ->label(FilamentUi::field('total_transaction_value'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
