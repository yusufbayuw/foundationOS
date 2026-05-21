<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class RfqVendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('References')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('request_for_quotation_id')
                            ->label(FilamentUi::field('request_for_quotation_id'))
                            ->relationship('requestForQuotation', 'id')
                            ->required(),
                        Select::make('vendor_id')
                            ->label(FilamentUi::field('vendor_id'))
                            ->relationship('vendor', 'name')
                            ->required(),
                    ]),

                Section::make('Invitation & Response')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('invitation_date')
                            ->label(FilamentUi::field('invitation_date')),
                        DatePicker::make('response_deadline')
                            ->label(FilamentUi::field('response_deadline')),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('invited'),
                        DateTimePicker::make('responded_at'),
                        TextInput::make('quotation_amount')
                            ->label(FilamentUi::field('quotation_amount'))
                            ->numeric(),
                        TextInput::make('quotation_document')
                            ->label(FilamentUi::field('quotation_document')),
                    ]),

                Section::make('Evaluation')
                    ->columns(2)
                    ->schema([
                        TextInput::make('technical_score')
                            ->label(FilamentUi::field('technical_score'))
                            ->numeric(),
                        TextInput::make('price_score')
                            ->label(FilamentUi::field('price_score'))
                            ->numeric(),
                        TextInput::make('total_score')
                            ->label(FilamentUi::field('total_score'))
                            ->numeric(),
                        TextInput::make('ranking')
                            ->label(FilamentUi::field('ranking'))
                            ->numeric(),
                        Toggle::make('is_shortlisted')
                            ->label(FilamentUi::field('is_shortlisted'))
                            ->required(),
                        Toggle::make('is_awarded')
                            ->label(FilamentUi::field('is_awarded'))
                            ->required(),
                        Textarea::make('award_reason')
                            ->label(FilamentUi::field('award_reason'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
