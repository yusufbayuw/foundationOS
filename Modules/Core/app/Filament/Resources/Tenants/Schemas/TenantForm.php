<?php

namespace Modules\Core\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('uuid')
                            ->label(FilamentUi::text('UUID'))
                            ->required(),
                        TextInput::make('code')
                            ->label(FilamentUi::field('code'))
                            ->required(),
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('domain')
                            ->label(FilamentUi::field('domain')),
                        TextInput::make('subdomain')
                            ->label(FilamentUi::field('subdomain')),
                    ]),

                Section::make(FilamentUi::text('Branding'))
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label(FilamentUi::field('logo'))
                            ->image()
                            ->disk('public')
                            ->directory('tenants/logos'),
                        FileUpload::make('favicon')
                            ->label(FilamentUi::field('favicon'))
                            ->image()
                            ->disk('public')
                            ->directory('tenants/favicons'),
                        TextInput::make('primary_color')
                            ->label(FilamentUi::field('primary_color')),
                        TextInput::make('secondary_color')
                            ->label(FilamentUi::field('secondary_color')),
                    ]),

                Section::make(FilamentUi::text('Localization'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('timezone')
                            ->label(FilamentUi::field('timezone'))
                            ->required()
                            ->default('UTC'),
                        TextInput::make('currency')
                            ->label(FilamentUi::field('currency'))
                            ->required()
                            ->default('USD'),
                        TextInput::make('locale')
                            ->label(FilamentUi::field('locale'))
                            ->required()
                            ->default('en'),
                    ]),

                Section::make(FilamentUi::text('Subscription'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('billing_cycle')
                            ->label(FilamentUi::field('billing_cycle')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('trial'),
                        DateTimePicker::make('trial_ends_at'),
                        DateTimePicker::make('subscribed_at'),
                        DateTimePicker::make('subscription_expires_at'),
                        Select::make('subscription_plan_id')
                            ->label(FilamentUi::field('subscription_plan_id'))
                            ->relationship('subscriptionPlan', 'name'),
                    ]),

                Section::make(FilamentUi::text('Limits'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_users')
                            ->label(FilamentUi::field('max_users'))
                            ->numeric(),
                        TextInput::make('max_organizations')
                            ->label(FilamentUi::field('max_organizations'))
                            ->numeric(),
                        TextInput::make('max_storage_mb')
                            ->label(FilamentUi::field('max_storage_mb'))
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('SEO & Metadata'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(FilamentUi::field('meta_title')),
                        Textarea::make('meta_description')
                            ->label(FilamentUi::field('meta_description'))
                            ->columnSpanFull(),
                        Textarea::make('settings')
                            ->label(FilamentUi::field('settings'))
                            ->columnSpanFull(),
                        TextInput::make('created_by')
                            ->label(FilamentUi::field('created_by'))
                            ->numeric(),
                    ]),
            ]);
    }
}
