<?php

namespace Modules\School\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('schedule.id')
                            ->label(FilamentUi::text('Schedule'))
                            ->placeholder('-'),
                        TextEntry::make('student.id')
                            ->label(FilamentUi::text('Student')),
                        TextEntry::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('entity_type')
                            ->label(FilamentUi::field('entity_type')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('entry_method')
                            ->label(FilamentUi::field('entry_method'))
                            ->placeholder('-'),
                    ]),

                Section::make('Date & Time')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('attendance_date')
                            ->label(FilamentUi::field('attendance_date'))
                            ->date(),
                        TextEntry::make('check_in')
                            ->label(FilamentUi::field('check_in'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('check_out')
                            ->label(FilamentUi::field('check_out'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Verification Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('location_data')
                            ->label(FilamentUi::field('location_data'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('device_info')
                            ->label(FilamentUi::field('device_info'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('photo_proof')
                            ->label(FilamentUi::field('photo_proof'))
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
