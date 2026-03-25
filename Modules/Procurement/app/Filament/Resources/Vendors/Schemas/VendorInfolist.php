<?php

namespace Modules\Procurement\Filament\Resources\Vendors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('province.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Province'))
                    ->placeholder('-'),
                TextEntry::make('city.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('City'))
                    ->placeholder('-'),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                TextEntry::make('business_field')
                    ->label(\Modules\Core\Support\FilamentUi::field('business_field'))
                    ->placeholder('-'),
                TextEntry::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
                    ->placeholder('-'),
                TextEntry::make('nib')
                    ->label(\Modules\Core\Support\FilamentUi::field('nib'))
                    ->placeholder('-'),
                TextEntry::make('siup')
                    ->label(\Modules\Core\Support\FilamentUi::field('siup'))
                    ->placeholder('-'),
                TextEntry::make('tdp')
                    ->label(\Modules\Core\Support\FilamentUi::field('tdp'))
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code'))
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->placeholder('-'),
                TextEntry::make('website')
                    ->label(\Modules\Core\Support\FilamentUi::field('website'))
                    ->placeholder('-'),
                TextEntry::make('contact_person')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_person'))
                    ->placeholder('-'),
                TextEntry::make('contact_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_position'))
                    ->placeholder('-'),
                TextEntry::make('contact_phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_phone'))
                    ->placeholder('-'),
                TextEntry::make('contact_email')
                    ->label(\Modules\Core\Support\FilamentUi::field('contact_email'))
                    ->placeholder('-'),
                TextEntry::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->placeholder('-'),
                TextEntry::make('bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account'))
                    ->placeholder('-'),
                TextEntry::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder'))
                    ->placeholder('-'),
                TextEntry::make('tax_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('tax_status'))
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_blacklisted')
                    ->boolean(),
                TextEntry::make('blacklist_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('blacklist_reason'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('performance_rating')
                    ->label(\Modules\Core\Support\FilamentUi::field('performance_rating'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total_transactions')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transactions'))
                    ->numeric(),
                TextEntry::make('total_transaction_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_transaction_value'))
                    ->numeric(),
                TextEntry::make('documents')
                    ->label(\Modules\Core\Support\FilamentUi::field('documents'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
