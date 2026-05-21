<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AssessmentItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('assessment.name')
                            ->label(FilamentUi::text('Assessment')),
                        TextEntry::make('item_type')
                            ->label(FilamentUi::field('item_type')),
                        TextEntry::make('question_number')
                            ->label(FilamentUi::field('question_number'))
                            ->numeric(),
                    ]),

                Section::make('Question')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('question_text')
                            ->label(FilamentUi::field('question_text'))
                            ->columnSpanFull(),
                        TextEntry::make('question_attachment')
                            ->label(FilamentUi::field('question_attachment'))
                            ->placeholder('-'),
                        TextEntry::make('answer_options')
                            ->label(FilamentUi::field('answer_options'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('correct_answer')
                            ->label(FilamentUi::field('correct_answer'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Scoring')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('max_score')
                            ->label(FilamentUi::field('max_score'))
                            ->numeric(),
                        TextEntry::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->numeric(),
                        TextEntry::make('difficulty_level')
                            ->label(FilamentUi::field('difficulty_level'))
                            ->placeholder('-'),
                        TextEntry::make('cognitive_level')
                            ->label(FilamentUi::field('cognitive_level'))
                            ->placeholder('-'),
                        TextEntry::make('answer_key_rubric')
                            ->label(FilamentUi::field('answer_key_rubric'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
