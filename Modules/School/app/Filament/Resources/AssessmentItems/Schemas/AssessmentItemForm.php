<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AssessmentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('assessment_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('assessment_id'))
                    ->relationship('assessment', 'name')
                    ->required(),
                TextInput::make('item_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('item_type'))
                    ->required()
                    ->default('multiple_choice'),
                TextInput::make('question_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_number'))
                    ->required()
                    ->numeric(),
                Textarea::make('question_text')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_text'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('question_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_attachment')),
                Textarea::make('answer_options')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_options'))
                    ->columnSpanFull(),
                Textarea::make('correct_answer')
                    ->label(\Modules\Core\Support\FilamentUi::field('correct_answer'))
                    ->columnSpanFull(),
                TextInput::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->required()
                    ->numeric()
                    ->default(100),
                TextInput::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('difficulty_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('difficulty_level')),
                TextInput::make('cognitive_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('cognitive_level')),
                Textarea::make('answer_key_rubric')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_key_rubric'))
                    ->columnSpanFull(),
            ]);
    }
}
