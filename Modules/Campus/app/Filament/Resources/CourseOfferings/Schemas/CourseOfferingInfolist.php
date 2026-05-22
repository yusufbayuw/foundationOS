<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CourseOfferingInfolist
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
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('course.name')
                            ->label(FilamentUi::text('Course')),
                        TextEntry::make('academicPeriod.name')
                            ->label(FilamentUi::text('Academic period'))
                            ->placeholder('-'),
                        TextEntry::make('lecturer.id')
                            ->label(FilamentUi::text('Lecturer'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Offering Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('class_code')
                            ->label(FilamentUi::field('class_code')),
                        TextEntry::make('delivery_mode')
                            ->label(FilamentUi::field('delivery_mode')),
                        TextEntry::make('capacity')
                            ->label(FilamentUi::field('capacity'))
                            ->numeric(),
                        TextEntry::make('enrolled_count')
                            ->label(FilamentUi::field('enrolled_count'))
                            ->numeric(),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                    ]),

                Section::make(FilamentUi::text('Schedule'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('day_of_week')
                            ->label(FilamentUi::field('day_of_week'))
                            ->placeholder('-'),
                        TextEntry::make('room_name')
                            ->label(FilamentUi::field('room_name'))
                            ->placeholder('-'),
                        TextEntry::make('start_time')
                            ->label(FilamentUi::field('start_time'))
                            ->placeholder('-'),
                        TextEntry::make('end_time')
                            ->label(FilamentUi::field('end_time'))
                            ->placeholder('-'),
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
