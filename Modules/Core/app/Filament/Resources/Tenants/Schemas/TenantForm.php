<?php

namespace Modules\Core\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label(\Modules\Core\Support\FilamentUi::text('UUID'))
                    ->required(),
                TextInput::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->required(),
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('domain')
                    ->label(\Modules\Core\Support\FilamentUi::field('domain')),
                TextInput::make('subdomain')
                    ->label(\Modules\Core\Support\FilamentUi::field('subdomain')),
                TextInput::make('logo')
                    ->label(\Modules\Core\Support\FilamentUi::field('logo')),
                TextInput::make('favicon')
                    ->label(\Modules\Core\Support\FilamentUi::field('favicon')),
                TextInput::make('primary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('primary_color')),
                TextInput::make('secondary_color')
                    ->label(\Modules\Core\Support\FilamentUi::field('secondary_color')),
                TextInput::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone'))
                    ->required()
                    ->default('UTC'),
                TextInput::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->required()
                    ->default('USD'),
                TextInput::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale'))
                    ->required()
                    ->default('en'),
                TextInput::make('billing_cycle')
                    ->label(\Modules\Core\Support\FilamentUi::field('billing_cycle')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('trial'),
                DateTimePicker::make('trial_ends_at'),
                DateTimePicker::make('subscribed_at'),
                DateTimePicker::make('subscription_expires_at'),
                Textarea::make('settings')
                    ->label(\Modules\Core\Support\FilamentUi::field('settings'))
                    ->columnSpanFull(),
                TextInput::make('max_users')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_users'))
                    ->numeric(),
                TextInput::make('max_organizations')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_organizations'))
                    ->numeric(),
                TextInput::make('max_storage_mb')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_storage_mb'))
                    ->numeric(),
                TextInput::make('meta_title')
                    ->label(\Modules\Core\Support\FilamentUi::field('meta_title')),
                Textarea::make('meta_description')
                    ->label(\Modules\Core\Support\FilamentUi::field('meta_description'))
                    ->columnSpanFull(),
                Select::make('subscription_plan_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('subscription_plan_id'))
                    ->relationship('subscriptionPlan', 'name'),
                TextInput::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
                    ->numeric(),
            ]);
    }
}
