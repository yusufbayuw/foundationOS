<?php

namespace Modules\Finance\Filament\Resources\Payments\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Payment Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('student_invoice_id')
                            ->label(FilamentUi::field('student_invoice_id'))
                            ->relationship('studentInvoice', 'invoice_number', modifyQueryUsing: function ($query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->required(),
                        Select::make('chart_of_account_id')
                            ->label(FilamentUi::field('chart_of_account_id'))
                            ->relationship('chartOfAccount', 'name', modifyQueryUsing: function ($query): void {
                                if (Filament::getTenant()) {
                                    $query->where('tenant_id', Filament::getTenant()->getKey());
                                }
                            })
                            ->required(),
                        TextInput::make('payment_number')
                            ->label(FilamentUi::field('payment_number'))
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label(FilamentUi::field('payment_date'))
                            ->required(),
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Payment Method'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('payment_method')
                            ->label(FilamentUi::field('payment_method')),
                        TextInput::make('payment_channel')
                            ->label(FilamentUi::field('payment_channel')),
                        TextInput::make('reference_number')
                            ->label(FilamentUi::field('reference_number')),
                        TextInput::make('account_number')
                            ->label(FilamentUi::field('account_number')),
                        TextInput::make('account_holder')
                            ->label(FilamentUi::field('account_holder')),
                        TextInput::make('bank_name')
                            ->label(FilamentUi::field('bank_name')),
                        FileUpload::make('proof_file')
                            ->label(FilamentUi::field('proof_file'))
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->visibility('private')
                            ->directory('payment-proofs'),
                    ]),

                Section::make(FilamentUi::text('Verification'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('verified_by')
                            ->label(FilamentUi::field('verified_by'))
                            ->numeric(),
                        DateTimePicker::make('verified_at'),
                        Textarea::make('verification_notes')
                            ->label(FilamentUi::field('verification_notes'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Reconciliation'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('pending'),
                        Toggle::make('is_reconciled')
                            ->label(FilamentUi::field('is_reconciled'))
                            ->required(),
                        DatePicker::make('reconciliation_date')
                            ->label(FilamentUi::field('reconciliation_date')),
                    ]),
            ]);
    }
}
