<?php

namespace Modules\School\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('schedule.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Schedule'))
                    ->placeholder('-'),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('entity_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_type')),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('entry_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_method'))
                    ->placeholder('-'),
                TextEntry::make('attendance_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('attendance_date'))
                    ->date(),
                TextEntry::make('check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_in'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_out'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('location_data')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_data'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('device_info')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_info'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('photo_proof')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_proof'))
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
