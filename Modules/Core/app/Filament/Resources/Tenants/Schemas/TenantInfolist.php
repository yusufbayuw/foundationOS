<?php

namespace Modules\Core\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('uuid')
                    ->label(\Modules\Core\Support\FilamentUi::text('UUID')),
                TextEntry::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code')),
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('domain')
                    ->label(\Modules\Core\Support\FilamentUi::field('domain'))
                    ->placeholder('-'),
                TextEntry::make('subdomain')
                    ->label(\Modules\Core\Support\FilamentUi::field('subdomain'))
                    ->placeholder('-'),
                ImageEntry::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo'))
                    ->disk('public')
                    ->placeholder('-'),
                ImageEntry::make('favicon')
                    ->label(\Modules\Core\Support\FilamentUi::field('favicon'))
                    ->disk('public')
                    ->placeholder('-'),
                TextEntry::make('primary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('primary_color'))
                    ->placeholder('-'),
                TextEntry::make('secondary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('secondary_color'))
                    ->placeholder('-'),
                TextEntry::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone')),
                TextEntry::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency')),
                TextEntry::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale')),
                TextEntry::make('billing_cycle')
                    ->label(\Modules\Core\Support\FilamentUi::field('billing_cycle'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('trial_ends_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('trial_ends_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('subscribed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscribed_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('subscription_expires_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscription_expires_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_storage_mb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_mb'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('meta_title')
                    ->label(\Modules\Core\Support\FilamentUi::field('meta_title'))
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->label(\Modules\Core\Support\FilamentUi::field('meta_description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('subscriptionPlan.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Subscription plan'))
                    ->placeholder('-'),
                TextEntry::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
                    ->numeric()
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
