<?php

namespace Modules\Procurement\Filament\Resources\RequestForQuotations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class RequestForQuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('General Information'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('purchase_requisition_id')
                            ->label(FilamentUi::field('purchase_requisition_id'))
                            ->relationship('purchaseRequisition', 'id')
                            ->required(),
                        TextInput::make('created_by')
                            ->label(FilamentUi::field('created_by'))
                            ->numeric(),
                        TextInput::make('rfq_number')
                            ->label(FilamentUi::field('rfq_number'))
                            ->required(),
                        DatePicker::make('rfq_date')
                            ->label(FilamentUi::field('rfq_date'))
                            ->required(),
                        DatePicker::make('closing_date')
                            ->label(FilamentUi::field('closing_date')),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Budget & Status'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_estimated_budget')
                            ->label(FilamentUi::field('total_estimated_budget'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('currency')
                            ->label(FilamentUi::field('currency'))
                            ->required()
                            ->default('IDR'),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft'),
                        Textarea::make('award_criteria')
                            ->label(FilamentUi::field('award_criteria'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
