<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class StudentInvoiceItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('studentInvoice.id')
                            ->label(FilamentUi::text('Student invoice')),
                        TextEntry::make('tuitionType.name')
                            ->label(FilamentUi::text('Tuition type'))
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->numeric(),
                        TextEntry::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->money(),
                        TextEntry::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->numeric(),
                        TextEntry::make('penalty_amount')
                            ->label(FilamentUi::field('penalty_amount'))
                            ->numeric(),
                        TextEntry::make('subtotal')
                            ->label(FilamentUi::field('subtotal'))
                            ->numeric(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
