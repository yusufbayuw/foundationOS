<?php

namespace Modules\Finance\Filament\Resources\JournalEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name')
                    ->required(),
                TextInput::make('posted_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('posted_by'))
                    ->numeric(),
                Select::make('reversed_entry_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('reversed_entry_id'))
                    ->relationship('reversedEntry', 'id'),
                TextInput::make('entry_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('entry_number'))
                    ->required(),
                DatePicker::make('date')
                    ->label(\Modules\Core\Support\FilamentUi::field('date'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('total_debit')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_debit'))
                    ->required()
                    ->numeric(),
                TextInput::make('total_credit')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_credit'))
                    ->required()
                    ->numeric(),
                Toggle::make('is_balanced')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_balanced'))
                    ->required(),
                Toggle::make('is_posted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_posted'))
                    ->required(),
                DateTimePicker::make('posted_at'),
                Toggle::make('is_reversed')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reversed'))
                    ->required(),
                Textarea::make('reversal_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('reversal_reason'))
                    ->columnSpanFull(),
            ]);
    }
}
