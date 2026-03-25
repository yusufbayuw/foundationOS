<?php

namespace Modules\Core\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('username')
                    ->label(\Modules\Core\Support\FilamentUi::field('username'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(\Modules\Core\Support\FilamentUi::text('Email address'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(\Modules\Core\Support\FilamentUi::field('phone'))
                    ->searchable(),
                TextColumn::make('avatar')
                    ->label(\Modules\Core\Support\FilamentUi::field('avatar'))
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('email_verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_login_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_login_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_login_ip')
                    ->label(\Modules\Core\Support\FilamentUi::field('last_login_ip'))
                    ->searchable(),
                TextColumn::make('login_attempts')
                    ->label(\Modules\Core\Support\FilamentUi::field('login_attempts'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('locked_until')
                    ->label(\Modules\Core\Support\FilamentUi::field('locked_until'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label(\Modules\Core\Support\FilamentUi::field('timezone'))
                    ->searchable(),
                TextColumn::make('locale')
                    ->label(\Modules\Core\Support\FilamentUi::field('locale'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                                                        ]),
            ]);
    }
}
