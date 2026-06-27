<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Schemas;

use App\Support\TypedValue;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class CustomerInvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Invoice Details'))
                ->columns(3)
                ->schema([
                    TextEntry::make('invoice_number')
                        ->label(FilamentUi::field('invoice_number'))
                        ->copyable(),
                    TextEntry::make('invoice_type')
                        ->label(FilamentUi::field('invoice_type'))
                        ->badge(),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid' => 'success',
                            'issued', 'partial' => 'warning',
                            'void', 'cancelled' => 'danger',
                            default => 'gray',
                        }),
                    TextEntry::make('issue_date')
                        ->label(FilamentUi::field('issue_date'))
                        ->date(),
                    TextEntry::make('due_date')
                        ->label(FilamentUi::field('due_date'))
                        ->date(),
                    IconEntry::make('is_sent')
                        ->label(FilamentUi::field('is_sent'))
                        ->boolean(),
                ]),

            Section::make(FilamentUi::text('Customer Information'))
                ->columns(2)
                ->schema([
                    TextEntry::make('customer_name')
                        ->label(FilamentUi::field('customer_name')),
                    TextEntry::make('customer_email')
                        ->label(FilamentUi::field('customer_email'))
                        ->placeholder('-'),
                    TextEntry::make('customer_phone')
                        ->label(FilamentUi::field('customer_phone'))
                        ->placeholder('-'),
                    TextEntry::make('customer_address')
                        ->label(FilamentUi::field('customer_address'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make(FilamentUi::text('Amounts'))
                ->columns(3)
                ->schema([
                    TextEntry::make('subtotal')
                        ->label(FilamentUi::field('subtotal'))
                        ->money('IDR'),
                    TextEntry::make('discount_amount')
                        ->label(FilamentUi::field('discount_amount'))
                        ->money('IDR'),
                    TextEntry::make('tax_amount')
                        ->label(FilamentUi::field('tax_amount'))
                        ->money('IDR'),
                    TextEntry::make('total_amount')
                        ->label(FilamentUi::field('total_amount'))
                        ->money('IDR')
                        ->weight('bold'),
                    TextEntry::make('paid_amount')
                        ->label(FilamentUi::field('paid_amount'))
                        ->money('IDR'),
                    TextEntry::make('remaining_amount')
                        ->label(FilamentUi::field('remaining_amount'))
                        ->money('IDR')
                        ->color(fn ($state): string => TypedValue::float($state) > 0 ? 'danger' : 'success'),
                ]),

            Section::make(FilamentUi::text('Notes'))
                ->schema([
                    TextEntry::make('description')
                        ->label(FilamentUi::field('description'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                    TextEntry::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
