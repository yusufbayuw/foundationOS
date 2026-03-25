<?php

namespace Modules\Enrollment\Filament\Resources\ExamSchedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExamScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('admissionPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Admission period')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->date(),
                TextEntry::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->time(),
                TextEntry::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->time(),
                TextEntry::make('location')
                    ->label(\Modules\Core\Support\FilamentUi::field('location'))
                    ->placeholder('-'),
                TextEntry::make('room_capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_capacity'))
                    ->numeric(),
                TextEntry::make('registered_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('registered_count'))
                    ->numeric(),
                TextEntry::make('instructions')
                    ->label(\Modules\Core\Support\FilamentUi::field('instructions'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
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
