<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SchoolClassForm
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
                    ->relationship('academicPeriod', 'name')
                    ->required(),
                Select::make('department_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('department_id'))
                    ->relationship('department', 'name'),
                Select::make('homeroom_teacher_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('homeroom_teacher_id'))
                    ->relationship('homeroomTeacher', 'id'),
                Select::make('assistant_teacher_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assistant_teacher_id'))
                    ->relationship('assistantTeacher', 'id'),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('grade_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_level')),
                TextInput::make('capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('capacity'))
                    ->numeric(),
                TextInput::make('student_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
            ]);
    }
}
