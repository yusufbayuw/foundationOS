<?php

namespace Modules\Sales\Filament\Resources\CooperativeSavings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CooperativeSavingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('tenant_id')
                        ->label(FilamentUi::field('tenant_id'))
                        ->placeholder('-'),
                    TextEntry::make('customer_id')
                        ->label(FilamentUi::field('customer_id'))
                        ->placeholder('-'),
                    TextEntry::make('savings_type')
                        ->label(FilamentUi::field('savings_type'))
                        ->placeholder('-'),
                    TextEntry::make('amount')
                        ->label(FilamentUi::field('amount'))
                        ->placeholder('-'),
                    TextEntry::make('transaction_date')
                        ->label(FilamentUi::field('transaction_date'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('journal_entry_id')
                        ->label(FilamentUi::field('journal_entry_id'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
