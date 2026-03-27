<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class RequestForQuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('purchase_requisition_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('purchase_requisition_id'))
                    ->relationship('purchaseRequisition', 'id')
                    ->required(),
                TextInput::make('created_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_by'))
                    ->numeric(),
                TextInput::make('rfq_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_number'))
                    ->required(),
                DatePicker::make('rfq_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('rfq_date'))
                    ->required(),
                DatePicker::make('closing_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('closing_date')),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                TextInput::make('total_estimated_budget')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_estimated_budget'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('currency')
                    ->label(\Modules\Core\Support\FilamentUi::field('currency'))
                    ->required()
                    ->default('IDR'),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('draft'),
                Textarea::make('award_criteria')
                    ->label(\Modules\Core\Support\FilamentUi::field('award_criteria'))
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label(\Modules\Core\Support\FilamentUi::field('notes'))
                    ->columnSpanFull(),
            ]);
    }
}
