<?php

namespace Modules\Core\Filament\Resources\AcademicPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AcademicPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TenantField::organizationSelect(),
                        Select::make('academic_year_id')
                            ->label(FilamentUi::field('academic_year_id'))
                            ->relationship('academicYear', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                    ]),

                Section::make(FilamentUi::text('Period'))
                    ->columns(2)
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(FilamentUi::field('start_date'))
                            ->required(),
                        DatePicker::make('end_date')
                            ->label(FilamentUi::field('end_date'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Toggle::make('is_locked')
                            ->label(FilamentUi::field('is_locked'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
