<?php

namespace Modules\Campus\Filament\Resources\StudyPlanItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyPlanItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('studyPlan.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Study plan')),
                TextEntry::make('courseOffering.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Course offering'))
                    ->placeholder('-'),
                TextEntry::make('course.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Course'))
                    ->placeholder('-'),
                TextEntry::make('credits')
                    ->label(\Modules\Core\Support\FilamentUi::field('credits'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter'))
                    ->placeholder('-'),
                TextEntry::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('remarks')
                    ->label(\Modules\Core\Support\FilamentUi::field('remarks'))
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
