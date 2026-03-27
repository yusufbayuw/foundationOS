<?php

namespace Modules\School\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('schedule_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_id'))
                    ->relationship('schedule', 'id'),
                Select::make('student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                    ->relationship('student', 'id')
                    ->required(),
                TextInput::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric(),
                TextInput::make('entity_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_type'))
                    ->required()
                    ->default('student'),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required(),
                TextInput::make('entry_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_method')),
                DatePicker::make('attendance_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('attendance_date'))
                    ->required(),
                DateTimePicker::make('check_in'),
                DateTimePicker::make('check_out'),
                Textarea::make('location_data')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_data'))
                    ->columnSpanFull(),
                Textarea::make('device_info')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_info'))
                    ->columnSpanFull(),
                TextInput::make('photo_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_proof')),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
