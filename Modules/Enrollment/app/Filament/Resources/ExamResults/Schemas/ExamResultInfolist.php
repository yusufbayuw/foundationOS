<?php

namespace Modules\Enrollment\Filament\Resources\ExamResults\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Context')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('applicant.id')
                            ->label(FilamentUi::text('Applicant')),
                        TextEntry::make('examSchedule.name')
                            ->label(FilamentUi::text('Exam schedule'))
                            ->placeholder('-'),
                        TextEntry::make('examiner.name')
                            ->label(FilamentUi::text('Examiner'))
                            ->placeholder('-'),
                    ]),

                Section::make('Exam Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('seat_number')
                            ->label(FilamentUi::field('seat_number'))
                            ->placeholder('-'),
                        TextEntry::make('score')
                            ->label(FilamentUi::field('score'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('grade')
                            ->label(FilamentUi::field('grade'))
                            ->placeholder('-'),
                        IconEntry::make('is_passed')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('score_components')
                            ->label(FilamentUi::field('score_components'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Notes & Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
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
