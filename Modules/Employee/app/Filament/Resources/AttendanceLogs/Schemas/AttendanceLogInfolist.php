<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('employee.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Employee')),
                TextEntry::make('shift.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Shift'))
                    ->placeholder('-'),
                TextEntry::make('approved_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('approved_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date(),
                TextEntry::make('check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_in'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('check_out'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('work_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('work_hours'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('overtime_hours')
                    ->label(\Modules\Core\Support\FilamentUi::field('overtime_hours'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('location_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_check_in'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('location_check_out'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('device_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_in'))
                    ->placeholder('-'),
                TextEntry::make('device_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('device_check_out'))
                    ->placeholder('-'),
                TextEntry::make('photo_check_in')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_in'))
                    ->placeholder('-'),
                TextEntry::make('photo_check_out')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_check_out'))
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
