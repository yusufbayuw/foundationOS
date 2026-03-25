<?php

namespace Modules\Finance\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('student_invoice_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('student_invoice_id'))
                    ->relationship('studentInvoice', 'id')
                    ->required(),
                Select::make('chart_of_account_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('chart_of_account_id'))
                    ->relationship('chartOfAccount', 'name')
                    ->required(),
                TextInput::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric(),
                TextInput::make('payment_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_number'))
                    ->required(),
                DatePicker::make('payment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_date'))
                    ->required(),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method')),
                TextInput::make('payment_channel')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_channel')),
                TextInput::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number')),
                TextInput::make('account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_number')),
                TextInput::make('account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder')),
                TextInput::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name')),
                TextInput::make('proof_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('proof_file')),
                DateTimePicker::make('verified_at'),
                Textarea::make('verification_notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('verification_notes'))
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('pending'),
                Toggle::make('is_reconciled')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reconciled'))
                    ->required(),
                DatePicker::make('reconciliation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('reconciliation_date'))
            ]);
    }
}
