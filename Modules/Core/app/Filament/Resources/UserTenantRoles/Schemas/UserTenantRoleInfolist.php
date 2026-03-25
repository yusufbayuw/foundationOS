<?php

namespace Modules\Core\Filament\Resources\UserTenantRoles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserTenantRoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('User')),
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('tenantRole.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant role')),
                TextEntry::make('assigned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('assigned_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('assigned_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('assigned_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('expires_at'))
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_primary')
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
