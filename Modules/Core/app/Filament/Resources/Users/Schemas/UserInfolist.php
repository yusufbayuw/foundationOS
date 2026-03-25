<?php

namespace Modules\Core\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name')),
                TextEntry::make('username')
                    ->label(\Modules\Core\Support\FilamentUi::field('username'))
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address')),
                TextEntry::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->label(\Modules\Core\Support\FilamentUi::field('avatar'))
                    ->placeholder('-'),
                TextEntry::make('email_verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('email_verified_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('last_login_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_login_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('last_login_ip')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_login_ip'))
                    ->placeholder('-'),
                TextEntry::make('login_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('login_attempts'))
                    ->numeric(),
                TextEntry::make('locked_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('locked_until'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone')),
                TextEntry::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale')),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
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
