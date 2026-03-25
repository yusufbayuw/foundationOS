<?php

namespace Modules\Core\Filament\Resources\TenantModules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TenantModuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('module.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Module')),
                IconEntry::make('is_enabled')
                    ->boolean(),
                TextEntry::make('enabled_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('enabled_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('disabled_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('disabled_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
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
