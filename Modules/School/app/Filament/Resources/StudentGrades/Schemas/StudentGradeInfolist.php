<?php

namespace Modules\School\Filament\Resources\StudentGrades\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentGradeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                        TextEntry::make('assessment.name')
                            ->label(FilamentUi::text('Assessment')),
                        TextEntry::make('graded_by')
                            ->label(FilamentUi::field('graded_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Scoring'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric(),
                        TextEntry::make('score_letter')
                            ->label(FilamentUi::field('score_letter'))
                            ->placeholder('-'),
                        TextEntry::make('weight')
                            ->label(FilamentUi::field('weight'))
                            ->numeric(),
                        TextEntry::make('final_score')
                            ->label(FilamentUi::field('final_score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Status'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_passed')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('graded_at')
                            ->label(FilamentUi::field('graded_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        IconEntry::make('is_locked')
                            ->boolean(),
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
