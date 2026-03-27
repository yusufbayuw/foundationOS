<?php

namespace Modules\Library\Filament\Resources\BookReservations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Illuminate\Database\Eloquent\Builder;

class BookReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make(),
            Select::make('organization_id')
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable(),
            Select::make('book_id')
                ->relationship('book', 'title', modifyQueryUsing: function (Builder $query): void {
                    app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                })
                ->searchable()
                ->preload()
                ->required(),
            Select::make('member_id')
                ->relationship('member', 'member_number', modifyQueryUsing: function (Builder $query): void {
                    app(\Modules\Library\Support\LibraryScopeResolver::class)->apply($query, auth()->user());
                })
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('queue_position')->numeric()->required()->default(1),
            TextInput::make('status')->required()->default('pending'),
            DateTimePicker::make('requested_at'),
            DateTimePicker::make('ready_at'),
            DateTimePicker::make('expires_at'),
            DateTimePicker::make('fulfilled_at'),
            DateTimePicker::make('cancelled_at'),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }
}
