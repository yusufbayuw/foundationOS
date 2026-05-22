<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentInvoiceItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Item Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('student_invoice_id')
                            ->label(FilamentUi::field('student_invoice_id'))
                            ->relationship('studentInvoice', 'id')
                            ->required(),
                        Select::make('tuition_type_id')
                            ->label(FilamentUi::field('tuition_type_id'))
                            ->relationship('tuitionType', 'name'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Pricing'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity')
                            ->label(FilamentUi::field('quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('unit_price')
                            ->label(FilamentUi::field('unit_price'))
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('penalty_amount')
                            ->label(FilamentUi::field('penalty_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('subtotal')
                            ->label(FilamentUi::field('subtotal'))
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }
}
