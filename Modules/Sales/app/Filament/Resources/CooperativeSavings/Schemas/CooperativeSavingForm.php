<?php

namespace Modules\Sales\Filament\Resources\CooperativeSavings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class CooperativeSavingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TenantField::make(),
                    TextInput::make('customer_id')
                        ->label(FilamentUi::field('customer_id'))
                        ->numeric(),
                    TextInput::make('savings_type')
                        ->label(FilamentUi::field('savings_type')),
                    TextInput::make('amount')
                        ->label(FilamentUi::field('amount'))
                        ->numeric(),
                    TextInput::make('transaction_date')
                        ->label(FilamentUi::field('transaction_date')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('journal_entry_id')
                        ->label(FilamentUi::field('journal_entry_id'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }
}
