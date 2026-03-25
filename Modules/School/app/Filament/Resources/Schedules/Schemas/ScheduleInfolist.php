<?php

namespace Modules\School\Filament\Resources\Schedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period'))
                    ->placeholder('-'),
                TextEntry::make('class_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_id'))
                    ->numeric(),
                TextEntry::make('subject.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Subject')),
                TextEntry::make('teacher.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Teacher'))
                    ->placeholder('-'),
                TextEntry::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week')),
                TextEntry::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->time(),
                TextEntry::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->time(),
                TextEntry::make('duration_minutes')
                    ->label(\Modules\Core\Support\FilamentUi::field('duration_minutes'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('schedule_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('schedule_type'))
                    ->placeholder('-'),
                TextEntry::make('semester')
                    ->label(\Modules\Core\Support\FilamentUi::field('semester'))
                    ->placeholder('-'),
                IconEntry::make('is_recurring')
                    ->boolean(),
                TextEntry::make('effective_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('effective_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('expiry_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('expiry_date'))
                    ->date()
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
