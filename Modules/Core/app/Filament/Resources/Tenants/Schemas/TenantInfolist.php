<?php

namespace Modules\Core\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('uuid')
                            ->label(FilamentUi::text('UUID')),
                        TextEntry::make('code')
                            ->label(FilamentUi::field('code')),
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('domain')
                            ->label(FilamentUi::field('domain'))
                            ->placeholder('-'),
                        TextEntry::make('subdomain')
                            ->label(FilamentUi::field('subdomain'))
                            ->placeholder('-'),
                    ]),

                Section::make('Branding')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('logo')
                            ->label(FilamentUi::field('logo'))
                            ->disk('public')
                            ->placeholder('-'),
                        ImageEntry::make('favicon')
                            ->label(FilamentUi::field('favicon'))
                            ->disk('public')
                            ->placeholder('-'),
                        TextEntry::make('primary_color')
                            ->label(FilamentUi::field('primary_color'))
                            ->placeholder('-'),
                        TextEntry::make('secondary_color')
                            ->label(FilamentUi::field('secondary_color'))
                            ->placeholder('-'),
                    ]),

                Section::make('Localization')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('timezone')
                            ->label(FilamentUi::field('timezone')),
                        TextEntry::make('currency')
                            ->label(FilamentUi::field('currency')),
                        TextEntry::make('locale')
                            ->label(FilamentUi::field('locale')),
                    ]),

                Section::make('Subscription')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('billing_cycle')
                            ->label(FilamentUi::field('billing_cycle'))
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('trial_ends_at')
                            ->label(FilamentUi::field('trial_ends_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('subscribed_at')
                            ->label(FilamentUi::field('subscribed_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('subscription_expires_at')
                            ->label(FilamentUi::field('subscription_expires_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('subscriptionPlan.name')
                            ->label(FilamentUi::text('Subscription plan'))
                            ->placeholder('-'),
                    ]),

                Section::make('Limits')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('max_users')
                            ->label(FilamentUi::field('max_users'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_organizations')
                            ->label(FilamentUi::field('max_organizations'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('max_storage_mb')
                            ->label(FilamentUi::field('max_storage_mb'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make('SEO & Metadata')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('meta_title')
                            ->label(FilamentUi::field('meta_title'))
                            ->placeholder('-'),
                        TextEntry::make('meta_description')
                            ->label(FilamentUi::field('meta_description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('settings')
                            ->label(FilamentUi::field('settings'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('created_by')
                            ->label(FilamentUi::field('created_by'))
                            ->numeric()
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
