<?php

namespace Modules\Campus\Filament\Resources\Theses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ThesisInfolist
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
                        TextEntry::make('collageStudent.id')
                            ->label(FilamentUi::text('Collage student')),
                        TextEntry::make('advisorLecturer.id')
                            ->label(FilamentUi::text('Advisor lecturer'))
                            ->placeholder('-'),
                        TextEntry::make('examinerLecturer.id')
                            ->label(FilamentUi::text('Examiner lecturer'))
                            ->placeholder('-'),
                    ]),

                Section::make('Thesis Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label(FilamentUi::field('title')),
                        TextEntry::make('research_area')
                            ->label(FilamentUi::field('research_area'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('document_path')
                            ->label(FilamentUi::field('document_path'))
                            ->placeholder('-'),
                    ]),

                Section::make('Timeline')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('proposal_submitted_at')
                            ->label(FilamentUi::field('proposal_submitted_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('defense_date')
                            ->label(FilamentUi::field('defense_date'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Grade & Notes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter'))
                            ->placeholder('-'),
                        TextEntry::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric()
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
