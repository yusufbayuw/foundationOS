<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Schemas;

use App\Support\TypedValue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\CustomerInvoice;

class CustomerInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('Invoice Details'))
                ->columns(2)
                ->schema([
                    TenantField::make(),
                    TextInput::make('invoice_number')
                        ->label(FilamentUi::field('invoice_number'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(fn () => 'INV-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT)),
                    Select::make('invoice_type')
                        ->label(FilamentUi::field('invoice_type'))
                        ->options(self::normalizeOptions(CustomerInvoice::invoiceTypeOptions()))
                        ->required()
                        ->default('general'),
                    Select::make('status')
                        ->label(FilamentUi::field('status'))
                        ->options(self::normalizeOptions(CustomerInvoice::statusOptions()))
                        ->required()
                        ->default('draft'),
                    DatePicker::make('issue_date')
                        ->label(FilamentUi::field('issue_date'))
                        ->required()
                        ->default(today()),
                    DatePicker::make('due_date')
                        ->label(FilamentUi::field('due_date'))
                        ->required()
                        ->default(today()->addDays(30)),
                ]),

            Section::make(FilamentUi::text('Customer Information'))
                ->columns(2)
                ->schema([
                    TextInput::make('customer_name')
                        ->label(FilamentUi::field('customer_name'))
                        ->required(),
                    TextInput::make('customer_email')
                        ->label(FilamentUi::field('customer_email'))
                        ->email(),
                    TextInput::make('customer_phone')
                        ->label(FilamentUi::field('customer_phone'))
                        ->tel(),
                    Textarea::make('customer_address')
                        ->label(FilamentUi::field('customer_address'))
                        ->columnSpanFull(),
                ]),

            Section::make(FilamentUi::text('Amounts'))
                ->columns(2)
                ->schema([
                    TextInput::make('subtotal')
                        ->label(FilamentUi::field('subtotal'))
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                    TextInput::make('discount_amount')
                        ->label(FilamentUi::field('discount_amount'))
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                    TextInput::make('discount_reason')
                        ->label(FilamentUi::field('discount_reason')),
                    TextInput::make('tax_amount')
                        ->label(FilamentUi::field('tax_amount'))
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                    TextInput::make('total_amount')
                        ->label(FilamentUi::field('total_amount'))
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),
                    TextInput::make('paid_amount')
                        ->label(FilamentUi::field('paid_amount'))
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),
                ]),

            Section::make(FilamentUi::text('Notes'))
                ->schema([
                    Textarea::make('description')
                        ->label(FilamentUi::field('description'))
                        ->columnSpanFull(),
                    Textarea::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /**
     * @param  array<mixed, mixed>  $options
     * @return array<string, string>
     */
    private static function normalizeOptions(array $options): array
    {
        $normalized = [];

        foreach ($options as $key => $value) {
            $normalized[TypedValue::string($key)] = TypedValue::string($value);
        }

        return $normalized;
    }
}
