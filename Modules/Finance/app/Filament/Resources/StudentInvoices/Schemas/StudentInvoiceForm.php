<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Schemas;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StudentInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Invoice Details'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('tuition_type_id')
                            ->label(FilamentUi::field('tuition_type_id'))
                            ->relationship('tuitionType', 'name', modifyQueryUsing: function (Builder $query): void {
                                if (Filament::getTenant()) {
                                    $query->where($query->getModel()->qualifyColumn('tenant_id'), TypedValue::tenantKey(Filament::getTenant()->getKey()));
                                }
                            }),
                        TextInput::make('invoice_number')
                            ->label(FilamentUi::field('invoice_number'))
                            ->required(),
                        TextInput::make('invoice_type')
                            ->label(FilamentUi::field('invoice_type')),
                        DatePicker::make('issue_date')
                            ->label(FilamentUi::field('issue_date'))
                            ->required(),
                        DatePicker::make('due_date')
                            ->label(FilamentUi::field('due_date'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                    ]),

                Section::make(FilamentUi::text('Amount'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->required()
                            ->numeric(),
                        TextInput::make('discount_amount')
                            ->label(FilamentUi::field('discount_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('discount_reason')
                            ->label(FilamentUi::field('discount_reason'))
                            ->columnSpanFull(),
                        TextInput::make('penalty_amount')
                            ->label(FilamentUi::field('penalty_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_amount')
                            ->label(FilamentUi::field('total_amount'))
                            ->required()
                            ->numeric(),
                        TextInput::make('paid_amount')
                            ->label(FilamentUi::field('paid_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('remaining_amount')
                            ->label(FilamentUi::field('remaining_amount'))
                            ->required()
                            ->numeric(),
                    ]),

                Section::make(FilamentUi::text('Notes'))
                    ->columns(1)
                    ->schema([
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Delivery'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_sent')
                            ->label(FilamentUi::field('is_sent'))
                            ->required(),
                        DateTimePicker::make('sent_at'),
                        TextInput::make('sent_via')
                            ->label(FilamentUi::field('sent_via')),
                        TextInput::make('invoiceable_type')
                            ->label(FilamentUi::field('invoiceable_type'))
                            ->required(),
                        TextInput::make('invoiceable_id')
                            ->label(FilamentUi::field('invoiceable_id'))
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }
}
