<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseOfferingInfolist
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
                TextEntry::make('course.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Course')),
                TextEntry::make('academicPeriod.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic period'))
                    ->placeholder('-'),
                TextEntry::make('lecturer.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Lecturer'))
                    ->placeholder('-'),
                TextEntry::make('class_code')
                    ->label(\Modules\Core\Support\FilamentUi::field('class_code')),
                TextEntry::make('capacity')
                    ->label(\Modules\Core\Support\FilamentUi::field('capacity'))
                    ->numeric(),
                TextEntry::make('enrolled_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('enrolled_count'))
                    ->numeric(),
                TextEntry::make('delivery_mode')
                    ->label(\Modules\Core\Support\FilamentUi::field('delivery_mode')),
                TextEntry::make('day_of_week')
                    ->label(\Modules\Core\Support\FilamentUi::field('day_of_week'))
                    ->placeholder('-'),
                TextEntry::make('start_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('start_time'))
                    ->placeholder('-'),
                TextEntry::make('end_time')
                    ->label(\Modules\Core\Support\FilamentUi::field('end_time'))
                    ->placeholder('-'),
                TextEntry::make('room_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('room_name'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
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
