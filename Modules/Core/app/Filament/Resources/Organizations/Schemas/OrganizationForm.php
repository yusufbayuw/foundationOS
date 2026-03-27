<?php

namespace Modules\Core\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('short_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('short_name')),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                TextInput::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level')),
                TextInput::make('npsn')
                    ->label(\Modules\Core\Support\FilamentUi::field('npsn')),
                TextInput::make('nss')
                    ->label(\Modules\Core\Support\FilamentUi::field('nss')),
                TextInput::make('accreditation_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('accreditation_status')),
                TextInput::make('npwp')
                    ->label(\Modules\Core\Support\FilamentUi::field('npwp')),
                TextInput::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->tel(),
                TextInput::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->email(),
                TextInput::make('website')
                    ->label(\Modules\Core\Support\FilamentUi::field('website'))
                    ->url(),
                Textarea::make('address')
                    ->label(\Modules\Core\Support\FilamentUi::field('address'))
                    ->columnSpanFull(),
                Select::make('province_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('province_id'))
                    ->relationship('province', 'name'),
                Select::make('city_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('city_id'))
                    ->relationship('city', 'name'),
                Select::make('district_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('district_id'))
                    ->relationship('district', 'name'),
                Select::make('village_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('village_id'))
                    ->relationship('village', 'name'),
                TextInput::make('postal_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('postal_code')),
                TextInput::make('latitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('latitude'))
                    ->numeric(),
                TextInput::make('longitude')
                    ->label(\Modules\Core\Support\FilamentUi::field('longitude'))
                    ->numeric(),
                DatePicker::make('established_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('established_date')),
                Select::make('principal_user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('principal_user_id'))
                    ->relationship('principalUser', 'name'),
                FileUpload::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo'))
                    ->image()
                    ->disk('public')
                    ->directory('organizations/logos'),
                FileUpload::make('stamp')
                    ->label(\Modules\Core\Support\FilamentUi::field('stamp'))
                    ->image()
                    ->disk('public')
                    ->directory('organizations/stamps'),
                FileUpload::make('signature')
                    ->label(\Modules\Core\Support\FilamentUi::field('signature'))
                    ->image()
                    ->disk('public')
                    ->directory('organizations/signatures'),
                FileUpload::make('letterhead')
                    ->label(\Modules\Core\Support\FilamentUi::field('letterhead'))
                    ->image()
                    ->disk('public')
                    ->directory('organizations/letterheads'),
                Toggle::make('is_main')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_main'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Textarea::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
                    ->columnSpanFull(),
            ]);
    }
}
