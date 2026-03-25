<?php

namespace Modules\Employee\Filament\Resources\KpiIndicators\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KpiIndicatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->placeholder('-'),
                TextEntry::make('measurement_unit')
                    ->label(\Modules\Core\Support\FilamentUi::field('measurement_unit'))
                    ->placeholder('-'),
                TextEntry::make('target_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_type'))
                    ->placeholder('-'),
                TextEntry::make('target_value')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_value'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('target_minimum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_minimum'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('target_maximum')
                    ->label(\Modules\Core\Support\FilamentUi::field('target_maximum'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('weight_percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('weight_percentage'))
                    ->numeric(),
                TextEntry::make('scoring_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('scoring_method'))
                    ->placeholder('-'),
                TextEntry::make('formula')
                    ->label(\Modules\Core\Support\FilamentUi::field('formula'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('data_source')
                    ->label(\Modules\Core\Support\FilamentUi::field('data_source'))
                    ->placeholder('-'),
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
