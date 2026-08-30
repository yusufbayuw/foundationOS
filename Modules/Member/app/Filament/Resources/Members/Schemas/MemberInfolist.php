<?php

namespace Modules\Member\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Membership'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('member_number')
                            ->label(FilamentUi::text('Member number'))
                            ->copyable(),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('memberType.name')
                            ->label(FilamentUi::text('Member type'))
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label(FilamentUi::text('Registered at'))
                            ->dateTime(),
                    ]),
                Section::make(FilamentUi::text('Account'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')->label(FilamentUi::text('Name')),
                        TextEntry::make('user.email')->label(FilamentUi::text('Email')),
                    ]),
                Section::make(FilamentUi::text('Submitted profile'))
                    ->description('Profile details supplied by the member during registration.')
                    ->schema([
                        KeyValueEntry::make('profile.profile_data')
                            ->label(FilamentUi::text('Profile details'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(FilamentUi::text('Verification'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('verifier.name')
                            ->label(FilamentUi::text('Reviewed by'))
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label(FilamentUi::text('Reviewed at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label(FilamentUi::text('Rejection reason'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
