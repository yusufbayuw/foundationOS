<?php

namespace Modules\Core\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->required(),
                TextInput::make('username')
                    ->label(\Modules\Core\Support\FilamentUi::field('username')),
                TextInput::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->tel(),
                TextInput::make('password')
                    ->label(\Modules\Core\Support\FilamentUi::field('password'))
                    ->password()
                    ->required(),
                TextInput::make('avatar')
                    ->label(\Modules\Core\Support\FilamentUi::field('avatar')),
                DateTimePicker::make('email_verified_at'),
                DateTimePicker::make('last_login_at'),
                TextInput::make('last_login_ip')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_login_ip')),
                TextInput::make('login_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('login_attempts'))
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('locked_until'),
                TextInput::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone'))
                    ->required()
                    ->default('UTC'),
                TextInput::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale'))
                    ->required()
                    ->default('en'),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('active'),
            ]);
    }
}
