<?php

namespace Modules\Campus\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('studyProgram.name')
                            ->label(FilamentUi::text('Study program'))
                            ->placeholder('-'),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('course_type')
                            ->label(FilamentUi::field('course_type')),
                        TextEntry::make('semester_level')
                            ->label(FilamentUi::field('semester_level'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Credit Hours')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->numeric(),
                        TextEntry::make('theory_credits')
                            ->label(FilamentUi::field('theory_credits'))
                            ->numeric(),
                        TextEntry::make('practicum_credits')
                            ->label(FilamentUi::field('practicum_credits'))
                            ->numeric(),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_mandatory')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->boolean(),
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
