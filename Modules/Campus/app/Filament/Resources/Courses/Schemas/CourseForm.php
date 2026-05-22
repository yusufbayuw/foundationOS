<?php

namespace Modules\Campus\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Course Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('study_program_id')
                            ->label(FilamentUi::field('study_program_id'))
                            ->relationship('studyProgram', 'name'),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('course_type')
                            ->label(FilamentUi::field('course_type'))
                            ->required()
                            ->default('mandatory'),
                        TextInput::make('semester_level')
                            ->label(FilamentUi::field('semester_level'))
                            ->numeric(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Credit Hours'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('theory_credits')
                            ->label(FilamentUi::field('theory_credits'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('practicum_credits')
                            ->label(FilamentUi::field('practicum_credits'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_mandatory')
                            ->label(FilamentUi::field('is_mandatory'))
                            ->required(),
                        Toggle::make('is_active')
                            ->label(FilamentUi::field('is_active'))
                            ->required(),
                    ]),
            ]);
    }
}
