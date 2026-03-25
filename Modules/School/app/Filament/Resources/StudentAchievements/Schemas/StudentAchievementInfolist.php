<?php

namespace Modules\School\Filament\Resources\StudentAchievements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentAchievementInfolist
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
                TextEntry::make('academicYear.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Academic year'))
                    ->placeholder('-'),
                TextEntry::make('student.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Student')),
                TextEntry::make('achievementType.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Achievement type'))
                    ->placeholder('-'),
                TextEntry::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label(\Modules\Core\Support\FilamentUi::field('title')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('event_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_name'))
                    ->placeholder('-'),
                TextEntry::make('event_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_date'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('event_location')
                    ->label(\Modules\Core\Support\FilamentUi::field('event_location'))
                    ->placeholder('-'),
                TextEntry::make('organizer')
                    ->label(\Modules\Core\Support\FilamentUi::field('organizer'))
                    ->placeholder('-'),
                TextEntry::make('rank_position')
                    ->label(\Modules\Core\Support\FilamentUi::field('rank_position'))
                    ->placeholder('-'),
                TextEntry::make('certificate_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_number'))
                    ->placeholder('-'),
                TextEntry::make('certificate_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_file'))
                    ->placeholder('-'),
                TextEntry::make('photo_files')
                    ->label(\Modules\Core\Support\FilamentUi::field('photo_files'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('news_link')
                    ->label(\Modules\Core\Support\FilamentUi::field('news_link'))
                    ->placeholder('-'),
                TextEntry::make('points_earned')
                    ->label(\Modules\Core\Support\FilamentUi::field('points_earned'))
                    ->numeric(),
                TextEntry::make('verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_featured')
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
