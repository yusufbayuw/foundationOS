<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CourseOfferingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Course & Period'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('course_id')
                            ->label(FilamentUi::field('course_id'))
                            ->relationship('course', 'name')
                            ->required(),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name'),
                        Select::make('lecturer_id')
                            ->label(FilamentUi::field('lecturer_id'))
                            ->relationship('lecturer', 'id'),
                        TextInput::make('class_code')
                            ->label(FilamentUi::field('class_code'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Capacity & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('capacity')
                            ->label(FilamentUi::field('capacity'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('enrolled_count')
                            ->label(FilamentUi::field('enrolled_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('delivery_mode')
                            ->label(FilamentUi::field('delivery_mode'))
                            ->required()
                            ->default('offline'),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                    ]),

                Section::make(FilamentUi::text('Schedule & Location'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('day_of_week')
                            ->label(FilamentUi::field('day_of_week')),
                        TextInput::make('start_time')
                            ->label(FilamentUi::field('start_time')),
                        TextInput::make('end_time')
                            ->label(FilamentUi::field('end_time')),
                        TextInput::make('room_name')
                            ->label(FilamentUi::field('room_name')),
                    ]),
            ]);
    }
}
