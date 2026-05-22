<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AttendanceLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Employee & Shift'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('employee.id')
                            ->label(FilamentUi::text('Employee')),
                        TextEntry::make('shift.name')
                            ->label(FilamentUi::text('Shift'))
                            ->placeholder('-'),
                        TextEntry::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Attendance Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('date')
                            ->label(FilamentUi::field('date'))
                            ->date(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('check_in')
                            ->label(FilamentUi::field('check_in'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('check_out')
                            ->label(FilamentUi::field('check_out'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('work_hours')
                            ->label(FilamentUi::field('work_hours'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('overtime_hours')
                            ->label(FilamentUi::field('overtime_hours'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Location & Device'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('location_check_in')
                            ->label(FilamentUi::field('location_check_in'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('location_check_out')
                            ->label(FilamentUi::field('location_check_out'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('device_check_in')
                            ->label(FilamentUi::field('device_check_in'))
                            ->placeholder('-'),
                        TextEntry::make('device_check_out')
                            ->label(FilamentUi::field('device_check_out'))
                            ->placeholder('-'),
                        TextEntry::make('photo_check_in')
                            ->label(FilamentUi::field('photo_check_in'))
                            ->placeholder('-'),
                        TextEntry::make('photo_check_out')
                            ->label(FilamentUi::field('photo_check_out'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Notes & Timestamps'))
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
