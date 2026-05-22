<?php

namespace Modules\Core\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code'))
                            ->placeholder('-'),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('short_name')
                            ->label(FilamentUi::field('short_name'))
                            ->placeholder('-'),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        TextEntry::make('level')
                            ->label(FilamentUi::field('level'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Legal Documents'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('npsn')
                            ->label(FilamentUi::field('npsn'))
                            ->placeholder('-'),
                        TextEntry::make('nss')
                            ->label(FilamentUi::field('nss'))
                            ->placeholder('-'),
                        TextEntry::make('accreditation_status')
                            ->label(FilamentUi::field('accreditation_status'))
                            ->placeholder('-'),
                        TextEntry::make('npwp')
                            ->label(FilamentUi::field('npwp'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Contact'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->placeholder('-'),
                        TextEntry::make('website')
                            ->label(FilamentUi::field('website'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Address'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('address')
                            ->label(FilamentUi::field('address'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('province.name')
                            ->label(FilamentUi::text('Province'))
                            ->placeholder('-'),
                        TextEntry::make('city.name')
                            ->label(FilamentUi::text('City'))
                            ->placeholder('-'),
                        TextEntry::make('district.name')
                            ->label(FilamentUi::text('District'))
                            ->placeholder('-'),
                        TextEntry::make('village.name')
                            ->label(FilamentUi::text('Village'))
                            ->placeholder('-'),
                        TextEntry::make('postal_code')
                            ->label(FilamentUi::field('postal_code'))
                            ->placeholder('-'),
                        TextEntry::make('latitude')
                            ->label(FilamentUi::field('latitude'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('longitude')
                            ->label(FilamentUi::field('longitude'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Management'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('established_date')
                            ->label(FilamentUi::field('established_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('principalUser.name')
                            ->label(FilamentUi::text('Principal user'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Media'))
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('logo')
                            ->label(FilamentUi::field('logo'))
                            ->disk('public')
                            ->placeholder('-'),
                        ImageEntry::make('stamp')
                            ->label(FilamentUi::field('stamp'))
                            ->disk('public')
                            ->placeholder('-'),
                        ImageEntry::make('signature')
                            ->label(FilamentUi::field('signature'))
                            ->disk('public')
                            ->placeholder('-'),
                        ImageEntry::make('letterhead')
                            ->label(FilamentUi::field('letterhead'))
                            ->disk('public')
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Status & Settings'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_main')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
                        TextEntry::make('settings')
                            ->label(FilamentUi::field('settings'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
