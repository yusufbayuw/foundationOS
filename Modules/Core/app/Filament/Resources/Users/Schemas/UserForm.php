<?php

namespace Modules\Core\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
                            ->revealable()
                            ->autocomplete('new-password')
                            ->rules(UserPasswordPolicy::rules())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        DateTimePicker::make('email_verified_at')
                            ->label(FilamentUi::field('email_verified_at'))
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        DateTimePicker::make('last_login_at')
                            ->label(FilamentUi::field('last_login_at'))
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('last_login_ip')
                            ->label(FilamentUi::field('last_login_ip'))
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('login_attempts')
                            ->label(FilamentUi::field('login_attempts'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                        DateTimePicker::make('locked_until')
                            ->label(FilamentUi::field('locked_until'))
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ]),

                Section::make(FilamentUi::text('Preferences'))
                    ->columns(2)
                    ->schema([
                        Select::make('timezone')
                            ->label(FilamentUi::field('timezone'))
                            ->options(fn (): array => collect(timezone_identifiers_list())
                                ->mapWithKeys(fn (string $timezone): array => [$timezone => $timezone])
                                ->all())
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->default((string) config('app.timezone', 'Asia/Jakarta')),
                        Select::make('locale')
                            ->label(FilamentUi::field('locale'))
                            ->options([
                                'id' => 'Indonesia',
                                'en' => 'English',
                            ])
                            ->native(false)
                            ->required()
                            ->default((string) config('app.locale', 'id')),
                        Select::make('status')
                            ->label(FilamentUi::field('status'))
                            ->options([
                                'active' => FilamentUi::text('Active'),
                                'inactive' => FilamentUi::text('Inactive'),
                                'suspended' => FilamentUi::text('Suspended'),
                            ])
                            ->native(false)
                            ->required()
                            ->default('active'),
                    ]),
            ]);
    }
}
