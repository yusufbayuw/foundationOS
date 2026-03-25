<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('province_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('province_id'))
                    ->relationship('province', 'name'),
                Select::make('city_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('city_id'))
                    ->relationship('city', 'name'),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->required()
                    ->default('company'),
                TextInput::make('business_field')
                    ->label(\Modules\Core\Support\FilamentUi::field('business_field')),
                TextInput::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp')),
                TextInput::make('nib')
                    ->label(\Modules\Core\Support\FilamentUi::field('nib')),
                TextInput::make('siup')
                    ->label(\Modules\Core\Support\FilamentUi::field('siup')),
                TextInput::make('tdp')
                    ->label(\Modules\Core\Support\FilamentUi::field('tdp')),
                Textarea::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->columnSpanFull(),
                TextInput::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code')),
                TextInput::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->tel(),
                TextInput::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->email(),
                TextInput::make('website')
                    ->label(\Modules\Core\Support\FilamentUi::field('website'))
                    ->url(),
                TextInput::make('contact_person')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_person')),
                TextInput::make('contact_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_position')),
                TextInput::make('contact_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_phone'))
                    ->tel(),
                TextInput::make('contact_email')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_email'))
                    ->email(),
                TextInput::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name')),
                TextInput::make('bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account')),
                TextInput::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder')),
                TextInput::make('tax_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_status')),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Toggle::make('is_blacklisted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_blacklisted'))
                    ->required(),
                Textarea::make('blacklist_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('blacklist_reason'))
                    ->columnSpanFull(),
                TextInput::make('performance_rating')
                    ->label(\Modules\Core\Support\FilamentUi::field('performance_rating'))
                    ->numeric(),
                TextInput::make('total_transactions')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transactions'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_transaction_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transaction_value'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('documents')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents'))
                    ->columnSpanFull(),
            ]);
    }
}
