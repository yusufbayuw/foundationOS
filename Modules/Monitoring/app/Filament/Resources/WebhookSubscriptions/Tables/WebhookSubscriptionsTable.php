<?php

namespace Modules\Monitoring\Filament\Resources\WebhookSubscriptions\Tables;

use App\Support\TypedValue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class WebhookSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label(FilamentUi::field('url'))
                    ->searchable()
                    ->limit(60),
                TextColumn::make('events')
                    ->label(FilamentUi::field('events'))
                    ->formatStateUsing(fn ($state): string => is_array($state)
                        ? implode(', ', array_map(fn (mixed $event): string => TypedValue::string($event), $state))
                        : TypedValue::string($state))
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('description')
                    ->label(FilamentUi::field('description'))
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
