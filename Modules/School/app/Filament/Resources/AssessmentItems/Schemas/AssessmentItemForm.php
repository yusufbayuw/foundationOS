<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AssessmentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('assessment_id')
                            ->label(FilamentUi::field('assessment_id'))
                            ->relationship('assessment', 'name')
                            ->required(),
                        TextInput::make('item_type')
                            ->label(FilamentUi::field('item_type'))
                            ->required()
                            ->default('multiple_choice'),
                        TextInput::make('question_number')
                            ->label(FilamentUi::field('question_number'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make('Question')
                    ->columns(2)
                    ->schema([
                        Textarea::make('question_text')
                            ->label(FilamentUi::field('question_text'))
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('question_attachment')
                            ->label(FilamentUi::field('question_attachment')),
                        Textarea::make('answer_options')
                            ->label(FilamentUi::field('answer_options'))
                            ->columnSpanFull(),
                        Textarea::make('correct_answer')
                            ->label(FilamentUi::field('correct_answer'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Scoring')
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->required()
                            ->numeric()
                            ->default(100),
                        TextInput::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('difficulty_level')
                            ->label(FilamentUi::field('difficulty_level')),
                        TextInput::make('cognitive_level')
                            ->label(FilamentUi::field('cognitive_level')),
                        Textarea::make('answer_key_rubric')
                            ->label(FilamentUi::field('answer_key_rubric'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
