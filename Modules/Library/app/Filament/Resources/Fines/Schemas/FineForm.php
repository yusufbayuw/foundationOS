<?php

namespace Modules\Library\Filament\Resources\Fines\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Library\Support\LibraryScopeResolver;

class FineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scope')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->nullable()
                            ->helperText('Opsional. Kosongkan untuk denda tenant-wide.'),
                        Select::make('loan_id')
                            ->label(FilamentUi::field('loan_id'))
                            ->relationship('loan', 'id', modifyQueryUsing: function (Builder $query): void {
                                app(LibraryScopeResolver::class)->apply($query, auth()->user());
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Fine Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('fine_type')
                            ->label(FilamentUi::field('fine_type'))
                            ->required()
                            ->default('late_return'),
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->required()
                            ->numeric(),
                        TextInput::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('unpaid'),
                    ]),

                Section::make('Timeline & Notes')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('issued_at')
                            ->label(FilamentUi::field('issued_at')),
                        DatePicker::make('paid_at')
                            ->label(FilamentUi::field('paid_at')),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
