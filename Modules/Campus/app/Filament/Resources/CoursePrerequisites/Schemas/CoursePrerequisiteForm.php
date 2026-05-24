<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CoursePrerequisiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required(),
                Select::make('prerequisite_course_id')
                    ->relationship('prerequisiteCourse', 'name')
                    ->required(),
                TextInput::make('min_grade')
                    ->numeric(),
                Toggle::make('is_strict')
                    ->required(),
                Toggle::make('is_required')
                    ->required(),
                TextInput::make('note'),
            ]);
    }
}
