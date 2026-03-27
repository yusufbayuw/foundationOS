<?php

namespace Modules\School\Filament\Resources\StudentGrades\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentGradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('student_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_id'))
                    ->relationship('student', 'id')
                    ->required(),
                Select::make('assessment_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_id'))
                    ->relationship('assessment', 'name')
                    ->required(),
                TextInput::make('graded_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('graded_by'))
                    ->numeric(),
                TextInput::make('score')
                    ->label(\Modules\Core\Support\FilamentUi::field('score'))
                    ->required()
                    ->numeric(),
                TextInput::make('score_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('score_letter')),
                TextInput::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('final_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('final_score'))
                    ->numeric(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Toggle::make('is_passed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_passed')),
                DateTimePicker::make('graded_at'),
                Toggle::make('is_locked')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_locked'))
                    ->required(),
            ]);
    }
}
