<?php

namespace Modules\School\Filament\Resources\Curricula\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CurriculumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('type')
                            ->label(FilamentUi::field('type')),
                    ]),

                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        Textarea::make('grade_levels')
                            ->label(FilamentUi::field('grade_levels'))
                            ->columnSpanFull(),
                        DatePicker::make('effective_date')
                            ->label(FilamentUi::field('effective_date')),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
