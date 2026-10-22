<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Entry Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name', modifyQueryUsing: function ($query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->required(),
                        TextInput::make('entry_number')
                            ->label(FilamentUi::field('entry_number'))
                            ->required(),
                        DatePicker::make('date')
                            ->label(FilamentUi::field('date'))
                            ->required(),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Totals'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_debit')
                            ->label(FilamentUi::field('total_debit'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        TextInput::make('total_credit')
                            ->label(FilamentUi::field('total_credit'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                    ]),

            ]);
    }
}
