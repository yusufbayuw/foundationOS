<?php

namespace Modules\Campus\Filament\Resources\StudyResults\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyResultInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('studyPlanItem.id')
                    ->label(\Modules\Core\Support\FilamentUi::text('Study plan item'))
                    ->placeholder('-'),
                TextEntry::make('grade_letter')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_letter'))
                    ->placeholder('-'),
                TextEntry::make('grade_point')
                    ->label(\Modules\Core\Support\FilamentUi::field('grade_point'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('weight_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_score'))
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('passed')
                    ->boolean(),
                TextEntry::make('published_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('published_at'))
                    ->dateTime()
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
