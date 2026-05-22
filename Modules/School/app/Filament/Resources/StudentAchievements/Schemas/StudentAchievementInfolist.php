<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentAchievementInfolist
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
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('academicYear.name')
                            ->label(FilamentUi::text('Academic year'))
                            ->placeholder('-'),
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                        TextEntry::make('achievementType.name')
                            ->label(FilamentUi::text('Achievement type'))
                            ->placeholder('-'),
                        TextEntry::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Achievement Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label(FilamentUi::field('title')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('event_name')
                            ->label(FilamentUi::field('event_name'))
                            ->placeholder('-'),
                        TextEntry::make('event_date')
                            ->label(FilamentUi::field('event_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('event_location')
                            ->label(FilamentUi::field('event_location'))
                            ->placeholder('-'),
                        TextEntry::make('organizer')
                            ->label(FilamentUi::field('organizer'))
                            ->placeholder('-'),
                        TextEntry::make('rank_position')
                            ->label(FilamentUi::field('rank_position'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Certificate & Media'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('certificate_number')
                            ->label(FilamentUi::field('certificate_number'))
                            ->placeholder('-'),
                        TextEntry::make('certificate_file')
                            ->label(FilamentUi::field('certificate_file'))
                            ->placeholder('-'),
                        TextEntry::make('photo_files')
                            ->label(FilamentUi::field('photo_files'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('news_link')
                            ->label(FilamentUi::field('news_link'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Points & Verification'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('points_earned')
                            ->label(FilamentUi::field('points_earned'))
                            ->numeric(),
                        TextEntry::make('verified_at')
                            ->label(FilamentUi::field('verified_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        IconEntry::make('is_featured')
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
