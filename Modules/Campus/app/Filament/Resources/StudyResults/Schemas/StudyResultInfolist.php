<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudyResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('studyPlanItem.id')
                            ->label(FilamentUi::text('Study plan item'))
                            ->placeholder('-'),
                    ]),

                Section::make('Grade')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter'))
                            ->placeholder('-'),
                        TextEntry::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('weight_score')
                            ->label(FilamentUi::field('weight_score'))
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('passed')
                            ->boolean(),
                        TextEntry::make('published_at')
                            ->label(FilamentUi::field('published_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
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
