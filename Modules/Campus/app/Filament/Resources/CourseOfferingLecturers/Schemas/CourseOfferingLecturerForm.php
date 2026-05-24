<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Campus\Enums\CourseOfferingLecturerRole;

class CourseOfferingLecturerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('course_offering_id')
                    ->relationship('courseOffering', 'id')
                    ->required(),
                Select::make('lecturer_id')
                    ->relationship('lecturer', 'id')
                    ->required(),
                Select::make('role')
                    ->options(CourseOfferingLecturerRole::class)
                    ->default('primary')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('assigned_at'),
                DateTimePicker::make('removed_at'),
            ]);
    }
}
