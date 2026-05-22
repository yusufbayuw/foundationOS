<?php

namespace Modules\School\Filament\Resources\Schedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ScheduleInfolist
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
                        TextEntry::make('academicPeriod.name')
                            ->label(FilamentUi::text('Academic period'))
                            ->placeholder('-'),
                        TextEntry::make('class_id')
                            ->label(FilamentUi::field('class_id'))
                            ->numeric(),
                        TextEntry::make('subject.name')
                            ->label(FilamentUi::text('Subject')),
                        TextEntry::make('teacher.id')
                            ->label(FilamentUi::text('Teacher'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Schedule Time'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('day_of_week')
                            ->label(FilamentUi::field('day_of_week')),
                        TextEntry::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->time(),
                        TextEntry::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->time(),
                        TextEntry::make('duration_minutes')
                            ->label(FilamentUi::field('duration_minutes'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Configuration'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('schedule_type')
                            ->label(FilamentUi::field('schedule_type'))
                            ->placeholder('-'),
                        TextEntry::make('semester')
                            ->label(FilamentUi::field('semester'))
                            ->placeholder('-'),
                        IconEntry::make('is_recurring')
                            ->boolean(),
                        TextEntry::make('effective_date')
                            ->label(FilamentUi::field('effective_date'))
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('expiry_date')
                            ->label(FilamentUi::field('expiry_date'))
                            ->date()
                            ->placeholder('-'),
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
