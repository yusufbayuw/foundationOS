<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type'))
                            ->required()
                            ->default('company'),
                        TextInput::make('business_field')
                            ->label(FilamentUi::field('business_field')),
                        Select::make('province_id')
                            ->label(FilamentUi::field('province_id'))
                            ->relationship('province', 'name')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),
                        Select::make('city_id')
                            ->label(FilamentUi::field('city_id'))
                            ->relationship('city', 'name', fn (Builder $query, Get $get) => $query->when($get('province_id'), fn ($q, $id) => $q->where('province_id', $id))
                            ),
                        TextInput::make('postal_code')
                            ->label(FilamentUi::field('postal_code')),
                        Textarea::make('address')
                            ->label(FilamentUi::field('address'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Legal Documents')
                    ->columns(2)
                    ->schema([
                        TextInput::make('npwp')
                            ->label(FilamentUi::field('npwp')),
                        TextInput::make('nib')
                            ->label(FilamentUi::field('nib')),
                        TextInput::make('siup')
                            ->label(FilamentUi::field('siup')),
                        TextInput::make('tdp')
                            ->label(FilamentUi::field('tdp')),
                        TextInput::make('tax_status')
                            ->label(FilamentUi::field('tax_status')),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                        TextInput::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->email(),
                        TextInput::make('website')
                            ->label(FilamentUi::field('website'))
                            ->url(),
                    ]),

                Section::make('Contact Person')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_person')
                            ->label(FilamentUi::field('contact_person')),
                        TextInput::make('contact_position')
                            ->label(FilamentUi::field('contact_position')),
                        TextInput::make('contact_phone')
                            ->label(FilamentUi::field('contact_phone'))
                            ->tel(),
                        TextInput::make('contact_email')
                            ->label(FilamentUi::field('contact_email'))
                            ->email(),
                    ]),

                Section::make('Banking')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bank_name')
                            ->label(FilamentUi::field('bank_name')),
                        TextInput::make('bank_account')
                            ->label(FilamentUi::field('bank_account')),
                        TextInput::make('bank_account_holder')
                            ->label(FilamentUi::field('bank_account_holder')),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Toggle::make('is_blacklisted')
                            ->label(FilamentUi::field('is_blacklisted'))
                            ->required(),
                        Textarea::make('blacklist_reason')
                            ->label(FilamentUi::field('blacklist_reason'))
                            ->columnSpanFull(),
                        TextInput::make('performance_rating')
                            ->label(FilamentUi::field('performance_rating'))
                            ->numeric(),
                        TextInput::make('total_transactions')
                            ->label(FilamentUi::field('total_transactions'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_transaction_value')
                            ->label(FilamentUi::field('total_transaction_value'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('documents')
                            ->label(FilamentUi::field('documents'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
