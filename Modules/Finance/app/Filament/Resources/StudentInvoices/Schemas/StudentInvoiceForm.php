<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class StudentInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->default(Filament::getTenant()?->getKey())
                    ->disabled(Filament::getTenant() !== null)
                    ->dehydrated()
                    ->required(),
                Select::make('tuition_type_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tuition_type_id'))
                    ->relationship('tuitionType', 'name', modifyQueryUsing: function ($query): void {
                        if (Filament::getTenant()) {
                            $query->where('tenant_id', Filament::getTenant()->getKey());
                        }
                    }),
                TextInput::make('invoice_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_number'))
                    ->required(),
                TextInput::make('invoice_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_type')),
                DatePicker::make('issue_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('issue_date'))
                    ->required(),
                DatePicker::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->required(),
                TextInput::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('discount_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_reason'))
                    ->columnSpanFull(),
                TextInput::make('penalty_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('penalty_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('remaining_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('remaining_amount'))
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
                Toggle::make('is_sent')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_sent'))
                    ->required(),
                DateTimePicker::make('sent_at'),
                TextInput::make('sent_via')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_via')),
                TextInput::make('invoiceable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_type'))
                    ->required(),
                TextInput::make('invoiceable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_id'))
                    ->required()
                    ->numeric(),
            ]);
    }
}
