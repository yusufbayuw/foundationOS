<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class RfqVendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('request_for_quotation_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_for_quotation_id'))
                    ->relationship('requestForQuotation', 'id')
                    ->required(),
                Select::make('vendor_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('vendor_id'))
                    ->relationship('vendor', 'name')
                    ->required(),
                DatePicker::make('invitation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('invitation_date')),
                DatePicker::make('response_deadline')
                    ->label(\Modules\Core\Support\FilamentUi::field('response_deadline')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('invited'),
                DateTimePicker::make('responded_at'),
                TextInput::make('quotation_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_amount'))
                    ->numeric(),
                TextInput::make('quotation_document')
                    ->label(\Modules\Core\Support\FilamentUi::field('quotation_document')),
                TextInput::make('technical_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('technical_score'))
                    ->numeric(),
                TextInput::make('price_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('price_score'))
                    ->numeric(),
                TextInput::make('total_score')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_score'))
                    ->numeric(),
                TextInput::make('ranking')
                    ->label(\Modules\Core\Support\FilamentUi::field('ranking'))
                    ->numeric(),
                Toggle::make('is_shortlisted')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_shortlisted'))
                    ->required(),
                Toggle::make('is_awarded')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_awarded'))
                    ->required(),
                Textarea::make('award_reason')
                    ->label(\Modules\Core\Support\FilamentUi::field('award_reason'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
