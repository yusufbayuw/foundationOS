<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class ExamScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('admission_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('admission_period_id'))
                    ->relationship('admissionPeriod', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
                DatePicker::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->required(),
                TimePicker::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->required(),
                TimePicker::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->required(),
                TextInput::make('location')
                    ->label(\Modules\Core\Support\FilamentUi::field('location')),
                TextInput::make('room_capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_capacity'))
                    ->required()
                    ->numeric()
                    ->default(50),
                TextInput::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('instructions')
                    ->label(\Modules\Core\Support\FilamentUi::field('instructions'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
