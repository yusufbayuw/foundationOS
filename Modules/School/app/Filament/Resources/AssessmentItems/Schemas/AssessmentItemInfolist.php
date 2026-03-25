<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AssessmentItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('assessment.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Assessment')),
                TextEntry::make('item_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('item_type')),
                TextEntry::make('question_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_number'))
                    ->numeric(),
                TextEntry::make('question_text')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_text'))
                    ->columnSpanFull(),
                TextEntry::make('question_attachment')
                    ->label(\Modules\Core\Support\FilamentUi::field('question_attachment'))
                    ->placeholder('-'),
                TextEntry::make('answer_options')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_options'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('correct_answer')
                    ->label(\Modules\Core\Support\FilamentUi::field('correct_answer'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('max_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_score'))
                    ->numeric(),
                TextEntry::make('weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight'))
                    ->numeric(),
                TextEntry::make('difficulty_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('difficulty_level'))
                    ->placeholder('-'),
                TextEntry::make('cognitive_level')
                    ->label(\Modules\Core\Support\FilamentUi::field('cognitive_level'))
                    ->placeholder('-'),
                TextEntry::make('answer_key_rubric')
                    ->label(\Modules\Core\Support\FilamentUi::field('answer_key_rubric'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
