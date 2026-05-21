<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class GoodsReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Receipt Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('purchase_order_id')
                            ->label(FilamentUi::field('purchase_order_id'))
                            ->relationship('purchaseOrder', 'id'),
                        TextInput::make('receipt_number')
                            ->label(FilamentUi::field('receipt_number'))
                            ->required(),
                        DatePicker::make('receipt_date')
                            ->label(FilamentUi::field('receipt_date'))
                            ->required(),
                        TextInput::make('delivery_note_number')
                            ->label(FilamentUi::field('delivery_note_number')),
                        TextInput::make('supplier_delivery_number')
                            ->label(FilamentUi::field('supplier_delivery_number')),
                    ]),

                Section::make('Personnel & Status')
                    ->columns(2)
                    ->schema([
                        TextInput::make('received_by')
                            ->label(FilamentUi::field('received_by'))
                            ->numeric(),
                        TextInput::make('inspected_by')
                            ->label(FilamentUi::field('inspected_by'))
                            ->numeric(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('received_at'),
                    ]),

                Section::make('Notes')
                    ->columns(1)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                        Textarea::make('inspection_notes')
                            ->label(FilamentUi::field('inspection_notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
