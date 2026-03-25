<?php

namespace Modules\School\Filament\Resources\AchievementTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AchievementTypeInfolist
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
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->placeholder('-'),
                TextEntry::make('point_weight')
                    ->label(\Modules\Core\Support\FilamentUi::field('point_weight'))
                    ->numeric(),
                TextEntry::make('certificate_template')
                    ->label(\Modules\Core\Support\FilamentUi::field('certificate_template'))
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
