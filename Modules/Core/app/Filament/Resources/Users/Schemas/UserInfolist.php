<?php

namespace Modules\Core\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(FilamentUi::field('name')),
                        TextEntry::make('username')
                            ->label(FilamentUi::field('username'))
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label(FilamentUi::text('Email address')),
                        TextEntry::make('phone')
                            ->label(FilamentUi::field('phone'))
                            ->placeholder('-'),
                        TextEntry::make('avatar')
                            ->label(FilamentUi::field('avatar'))
                            ->placeholder('-'),
                    ]),

                Section::make('Authentication')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email_verified_at')
                            ->label(FilamentUi::field('email_verified_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('last_login_at')
                            ->label(FilamentUi::field('last_login_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('last_login_ip')
                            ->label(FilamentUi::field('last_login_ip'))
                            ->placeholder('-'),
                        TextEntry::make('login_attempts')
                            ->label(FilamentUi::field('login_attempts'))
                            ->numeric(),
                        TextEntry::make('locked_until')
                            ->label(FilamentUi::field('locked_until'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Preferences')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('timezone')
                            ->label(FilamentUi::field('timezone')),
                        TextEntry::make('locale')
                            ->label(FilamentUi::field('locale')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
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
