<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AdmissionPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Schedule'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->required(),
                        DatePicker::make('announcement_date')
                            ->label(FilamentUi::field('announcement_date')),
                    ]),

                Section::make(FilamentUi::text('Capacity & Fees'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('registration_fee')
                            ->label(FilamentUi::field('registration_fee'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('quota')
                            ->label(FilamentUi::field('quota'))
                            ->required()
                            ->numeric()
                            ->default(100),
                        TextInput::make('registered_count')
                            ->label(FilamentUi::field('registered_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('accepted_count')
                            ->label(FilamentUi::field('accepted_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Details'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('requirements')
                            ->label(FilamentUi::field('requirements'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
