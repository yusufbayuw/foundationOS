<?php

namespace Modules\Campus\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('study_program_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('study_program_id'))
                    ->relationship('studyProgram', 'name'),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('theory_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('theory_credits'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('practicum_credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('practicum_credits'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('semester_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester_level'))
                    ->numeric(),
                TextInput::make('course_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('course_type'))
                    ->required()
                    ->default('mandatory'),
                Toggle::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->required(),
            ]);
    }
}
