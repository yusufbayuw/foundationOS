<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SchoolClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Class Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                        Select::make('academic_period_id')
                            ->label(FilamentUi::field('academic_period_id'))
                            ->relationship('academicPeriod', 'name')
                            ->required(),
                        Select::make('department_id')
                            ->label(FilamentUi::field('department_id'))
                            ->relationship('department', 'name'),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('grade_level')
                            ->label(FilamentUi::field('grade_level')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Teachers & Capacity'))
                    ->columns(2)
                    ->schema([
                        Select::make('homeroom_teacher_id')
                            ->label(FilamentUi::field('homeroom_teacher_id'))
                            ->relationship('homeroomTeacher', 'id'),
                        Select::make('assistant_teacher_id')
                            ->label(FilamentUi::field('assistant_teacher_id'))
                            ->relationship('assistantTeacher', 'id'),
                        TextInput::make('capacity')
                            ->label(FilamentUi::field('capacity'))
                            ->numeric(),
                        TextInput::make('student_count')
                            ->label(FilamentUi::field('student_count'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
