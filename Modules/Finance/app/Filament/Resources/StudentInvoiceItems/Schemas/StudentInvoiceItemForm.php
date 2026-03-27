<?php

namespace Modules\Finance\Filament\Resources\StudentInvoiceItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentInvoiceItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('student_invoice_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_invoice_id'))
                    ->relationship('studentInvoice', 'id')
                    ->required(),
                Select::make('tuition_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tuition_type_id'))
                    ->relationship('tuitionType', 'name'),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label(\Modules\Core\Support\FilamentUi::field('quantity'))
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_price')
                    ->label(\Modules\Core\Support\FilamentUi::field('unit_price'))
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('penalty_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('penalty_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('subtotal')
                    ->label(\Modules\Core\Support\FilamentUi::field('subtotal'))
                    ->required()
                    ->numeric(),
            ]);
    }
}
