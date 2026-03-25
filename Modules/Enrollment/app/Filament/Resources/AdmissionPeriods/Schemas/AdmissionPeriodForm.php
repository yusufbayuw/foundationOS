<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdmissionPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                DatePicker::make('start_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_date'))
                    ->required(),
                DatePicker::make('announcement_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('announcement_date')),
                TextInput::make('registration_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_fee'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('quota')
                    ->label(\Modules\Core\Support\FilamentUi::field('quota'))
                    ->required()
                    ->numeric()
                    ->default(100),
                TextInput::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('accepted_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('accepted_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('requirements')
                    ->label(\Modules\Core\Support\FilamentUi::field('requirements'))
                    ->columnSpanFull(),
            ]);
    }
}
