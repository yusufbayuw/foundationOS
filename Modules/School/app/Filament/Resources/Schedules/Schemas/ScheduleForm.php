<?php

namespace Modules\School\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScheduleForm
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
                    ->relationship('organization', 'name'),
                Select::make('academic_period_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('academic_period_id'))
                    ->relationship('academicPeriod', 'name'),
                TextInput::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->required()
                    ->numeric(),
                Select::make('subject_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('subject_id'))
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('teacher_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('teacher_id'))
                    ->relationship('teacher', 'id'),
                TextInput::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week'))
                    ->required(),
                TimePicker::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->required(),
                TimePicker::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric(),
                TextInput::make('schedule_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_type')),
                TextInput::make('semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester')),
                Toggle::make('is_recurring')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_recurring'))
                    ->required(),
                DatePicker::make('effective_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('effective_date')),
                DatePicker::make('expiry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('expiry_date')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
