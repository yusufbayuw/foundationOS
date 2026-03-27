<?php

namespace Modules\Library\Filament\Resources\BookReservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BookReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->searchable(),
                TextColumn::make('organization.name')->searchable(),
                TextColumn::make('book.title')->searchable(),
                TextColumn::make('member.member_number')->searchable(),
                TextColumn::make('queue_position')->numeric()->sortable(),
                TextColumn::make('status')->badge()->searchable(),
                TextColumn::make('requested_at')->dateTime()->sortable(),
                TextColumn::make('expires_at')->dateTime()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
