<?php

namespace Modules\Core\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code')),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('short_name')
                            ->label(FilamentUi::field('short_name')),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                        TextInput::make('level')
                            ->label(FilamentUi::field('level')),
                    ]),

                Section::make('Legal Documents')
                    ->columns(2)
                    ->schema([
                        TextInput::make('npsn')
                            ->label(FilamentUi::field('npsn')),
                        TextInput::make('nss')
                            ->label(FilamentUi::field('nss')),
                        TextInput::make('accreditation_status')
                            ->label(FilamentUi::field('accreditation_status')),
                        TextInput::make('npwp')
                            ->label(FilamentUi::field('npwp')),
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

                Section::make('Address')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label(FilamentUi::field('address'))
                            ->columnSpanFull(),
                        Select::make('province_id')
                            ->label(FilamentUi::field('province_id'))
                            ->relationship('province', 'name')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),
                        Select::make('city_id')
                            ->label(FilamentUi::field('city_id'))
                            ->relationship('city', 'name', fn (Builder $query, Get $get) => $query->when($get('province_id'), fn ($q, $id) => $q->where('province_id', $id))
                            ),
                        Select::make('district_id')
                            ->label(FilamentUi::field('district_id'))
                            ->relationship('district', 'name'),
                        Select::make('village_id')
                            ->label(FilamentUi::field('village_id'))
                            ->relationship('village', 'name'),
                        TextInput::make('postal_code')
                            ->label(FilamentUi::field('postal_code')),
                        TextInput::make('latitude')
                            ->label(FilamentUi::field('latitude'))
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label(FilamentUi::field('longitude'))
                            ->numeric(),
                    ]),

                Section::make('Management')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('established_date')
                            ->label(FilamentUi::field('established_date')),
                        Select::make('principal_user_id')
                            ->label(FilamentUi::field('principal_user_id'))
                            ->relationship('principalUser', 'name'),
                    ]),

                Section::make('Media')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label(FilamentUi::field('logo'))
                            ->image()
                            ->disk('public')
                            ->directory('organizations/logos'),
                        FileUpload::make('stamp')
                            ->label(FilamentUi::field('stamp'))
                            ->image()
                            ->disk('public')
                            ->directory('organizations/stamps'),
                        FileUpload::make('signature')
                            ->label(FilamentUi::field('signature'))
                            ->image()
                            ->disk('public')
                            ->directory('organizations/signatures'),
                        FileUpload::make('letterhead')
                            ->label(FilamentUi::field('letterhead'))
                            ->image()
                            ->disk('public')
                            ->directory('organizations/letterheads'),
                    ]),

                Section::make('Status & Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_main')
                            ->label(FilamentUi::field('is_main'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Textarea::make('settings')
                            ->label(FilamentUi::field('settings'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
