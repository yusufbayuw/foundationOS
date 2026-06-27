<?php

namespace Modules\Library\Filament\Resources\BookReservations\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class BookReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Scope'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    Select::make('organization_id')
                        ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                            if (Filament::getTenant()) {
                                $query->where($query->getModel()->qualifyColumn('tenant_id'), Filament::getTenant()->getKey());
                            }
                        })
                        ->nullable(),
                ]),

            Section::make(FilamentUi::text('Reservation'))
                ->columns(2)
                ->schema([
                    Select::make('book_id')
                        ->relationship('book', 'title', modifyQueryUsing: function (Builder $query): void {
                            app(LibraryScopeResolver::class)->apply($query, auth()->user());
                        })
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('member_id')
                        ->relationship('member', 'member_number', modifyQueryUsing: function (Builder $query): void {
                            app(LibraryScopeResolver::class)->apply($query, auth()->user());
                        })
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('queue_position')->numeric()->required()->default(1),
                    TextInput::make('status')->required()->default('pending'),
                ]),

            Section::make(FilamentUi::text('Timeline'))
                ->columns(2)
                ->schema([
                    DateTimePicker::make('requested_at'),
                    DateTimePicker::make('ready_at'),
                    DateTimePicker::make('expires_at'),
                    DateTimePicker::make('fulfilled_at'),
                    DateTimePicker::make('cancelled_at'),
                    Textarea::make('notes')->columnSpanFull(),
                ]),
        ]);
    }
}
