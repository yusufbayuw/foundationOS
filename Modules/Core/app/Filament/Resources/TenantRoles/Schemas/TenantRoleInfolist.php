<?php

namespace Modules\Core\Filament\Resources\TenantRoles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TenantRoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('slug')
                    ->label(\Modules\Core\Support\FilamentUi::field('slug')),
                TextEntry::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->placeholder('-'),
                TextEntry::make('permissions')
                    ->label(\Modules\Core\Support\FilamentUi::field('permissions'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_default')
                    ->boolean(),
                IconEntry::make('is_super_admin')
                    ->boolean(),
                TextEntry::make('dashboard_route')
                    ->label(\Modules\Core\Support\FilamentUi::field('dashboard_route'))
                    ->placeholder('-'),
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
