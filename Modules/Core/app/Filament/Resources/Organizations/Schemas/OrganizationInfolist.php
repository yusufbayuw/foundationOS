<?php

namespace Modules\Core\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('short_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('short_name'))
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->placeholder('-'),
                TextEntry::make('npsn')
                    ->label(\Modules\Core\Support\FilamentUi::field('npsn'))
                    ->placeholder('-'),
                TextEntry::make('nss')
                    ->label(\Modules\Core\Support\FilamentUi::field('nss'))
                    ->placeholder('-'),
                TextEntry::make('accreditation_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('accreditation_status'))
                    ->placeholder('-'),
                TextEntry::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp'))
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
                TextEntry::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('province.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Province'))
                    ->placeholder('-'),
                TextEntry::make('city.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('City'))
                    ->placeholder('-'),
                TextEntry::make('district.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('District'))
                    ->placeholder('-'),
                TextEntry::make('village.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Village'))
                    ->placeholder('-'),
                TextEntry::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code'))
                    ->placeholder('-'),
                TextEntry::make('latitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('latitude'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('longitude'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('established_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('established_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('principalUser.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Principal user'))
                    ->placeholder('-'),
                ImageEntry::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo'))
                    ->disk('public')
                    ->placeholder('-'),
                ImageEntry::make('stamp')
                    ->label(\Modules\Core\Support\FilamentUi::field('stamp'))
                    ->disk('public')
                    ->placeholder('-'),
                ImageEntry::make('signature')
                    ->label(\Modules\Core\Support\FilamentUi::field('signature'))
                    ->disk('public')
                    ->placeholder('-'),
                ImageEntry::make('letterhead')
                    ->label(\Modules\Core\Support\FilamentUi::field('letterhead'))
                    ->disk('public')
                    ->placeholder('-'),
                IconEntry::make('is_main')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
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
