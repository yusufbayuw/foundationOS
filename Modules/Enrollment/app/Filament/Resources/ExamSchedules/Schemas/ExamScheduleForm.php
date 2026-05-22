<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class ExamScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('admission_period_id')
                            ->label(FilamentUi::field('admission_period_id'))
                            ->relationship('admissionPeriod', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
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
                        DatePicker::make('date')
                            ->label(FilamentUi::field('date'))
                            ->required(),
                        TextInput::make('location')
                            ->label(FilamentUi::field('location')),
                        TimePicker::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->required(),
                        TimePicker::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Capacity'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('room_capacity')
                            ->label(FilamentUi::field('room_capacity'))
                            ->required()
                            ->numeric()
                            ->default(50),
                        TextInput::make('registered_count')
                            ->label(FilamentUi::field('registered_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Instructions'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('instructions')
                            ->label(FilamentUi::field('instructions'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
