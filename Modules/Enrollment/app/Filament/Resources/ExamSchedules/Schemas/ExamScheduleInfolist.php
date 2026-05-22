<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class ExamScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('admissionPeriod.name')
                            ->label(FilamentUi::text('Admission period')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('type')
                            ->label(FilamentUi::field('type'))
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make(FilamentUi::text('Schedule'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('date')
                            ->label(FilamentUi::field('date'))
                            ->date(),
                        TextEntry::make('location')
                            ->label(FilamentUi::field('location'))
                            ->placeholder('-'),
                        TextEntry::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->time(),
                        TextEntry::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->time(),
                    ]),

                Section::make(FilamentUi::text('Capacity'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('room_capacity')
                            ->label(FilamentUi::field('room_capacity'))
                            ->numeric(),
                        TextEntry::make('registered_count')
                            ->label(FilamentUi::field('registered_count'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Instructions & Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('instructions')
                            ->label(FilamentUi::field('instructions'))
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
