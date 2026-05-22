<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudyPlanItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Relationships'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('studyPlan.id')
                            ->label(FilamentUi::text('Study plan')),
                        TextEntry::make('courseOffering.id')
                            ->label(FilamentUi::text('Course offering'))
                            ->placeholder('-'),
                        TextEntry::make('course.name')
                            ->label(FilamentUi::text('Course'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Enrollment Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('credits')
                            ->label(FilamentUi::field('credits'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make(FilamentUi::text('Grade'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('grade_letter')
                            ->label(FilamentUi::field('grade_letter'))
                            ->placeholder('-'),
                        TextEntry::make('grade_point')
                            ->label(FilamentUi::field('grade_point'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('remarks')
                            ->label(FilamentUi::field('remarks'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
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
