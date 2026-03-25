<?php

namespace Modules\Core\Filament\Resources\OrganizationSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization')),
                TextEntry::make('group')
                    ->label(\Modules\Core\Support\FilamentUi::field('group'))
                    ->placeholder('-'),
                TextEntry::make('key')
                    ->label(\Modules\Core\Support\FilamentUi::field('key')),
                TextEntry::make('value')
                    ->label(\Modules\Core\Support\FilamentUi::field('value'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type')),
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
