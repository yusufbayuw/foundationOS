<?php

namespace Modules\Core\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Core\Support\UserPasswordPolicy;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Basic Info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(FilamentUi::field('name'))
                            ->required(),
                        TextInput::make('username')
                            ->label(FilamentUi::field('username')),
                        TextInput::make('email')
                            ->label(FilamentUi::text('Email address'))
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->tel(),
                        TextInput::make('avatar')
                            ->label(FilamentUi::field('avatar')),
                    ]),

                Section::make(FilamentUi::text('Authentication'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label(FilamentUi::field('password'))
                            ->password()
                            ->rules(UserPasswordPolicy::rules())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        DateTimePicker::make('email_verified_at'),
                        DateTimePicker::make('last_login_at'),
                        TextInput::make('last_login_ip')
                            ->label(FilamentUi::field('last_login_ip')),
                        TextInput::make('login_attempts')
                            ->label(FilamentUi::field('login_attempts'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        DateTimePicker::make('locked_until'),
                    ]),

                Section::make(FilamentUi::text('Preferences'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('timezone')
                            ->label(FilamentUi::field('timezone'))
                            ->required()
                            ->default('UTC'),
                        TextInput::make('locale')
                            ->label(FilamentUi::field('locale'))
                            ->required()
                            ->default('en'),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('active'),
                    ]),
            ]);
    }
}
