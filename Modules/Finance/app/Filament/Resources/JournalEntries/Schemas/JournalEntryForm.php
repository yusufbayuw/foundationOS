<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                        Select::make('reversed_entry_id')
                            ->label(FilamentUi::field('reversed_entry_id'))
                            ->relationship('reversedEntry', 'id'),
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
                            ->numeric(),
                        TextInput::make('total_credit')
                            ->label(FilamentUi::field('total_credit'))
                            ->required()
                            ->numeric(),
                        Toggle::make('is_balanced')
                            ->label(FilamentUi::field('is_balanced'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Posting'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('posted_by')
                            ->label(FilamentUi::field('posted_by'))
                            ->numeric(),
                        DateTimePicker::make('posted_at'),
                        Toggle::make('is_posted')
                            ->label(FilamentUi::field('is_posted'))
                            ->required(),
                    ]),

                Section::make(FilamentUi::text('Reversal'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_reversed')
                            ->label(FilamentUi::field('is_reversed'))
                            ->required(),
                        Textarea::make('reversal_reason')
                            ->label(FilamentUi::field('reversal_reason'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
