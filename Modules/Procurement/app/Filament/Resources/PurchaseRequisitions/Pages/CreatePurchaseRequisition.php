<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;

class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('General Information')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        TextInput::make('requested_by')
                            ->label(FilamentUi::field('requested_by'))
                            ->numeric(),
                        TextInput::make('approved_by')
                            ->label(FilamentUi::field('approved_by'))
                            ->numeric()
                            ->disabled(),
                        TextInput::make('request_number')
                            ->label(FilamentUi::field('request_number'))
                            ->required(),
                        TextInput::make('priority')
                            ->label(FilamentUi::field('priority'))
                            ->required()
                            ->default('normal'),
                    ]),

                Step::make('Dates')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('request_date')
                            ->label(FilamentUi::field('request_date'))
                            ->required(),
                        DatePicker::make('required_date')
                            ->label(FilamentUi::field('required_date')),
                        DateTimePicker::make('approved_at')
                            ->disabled(),
                    ]),

                Step::make('Financial Summary')
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_items')
                            ->label(FilamentUi::field('total_items'))
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total_estimated_amount')
                            ->label(FilamentUi::field('total_estimated_amount'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Step::make('Status & Notes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('draft')
                            ->disabled()
                            ->dehydrated(),
                        Toggle::make('ready_for_sourcing')
                            ->label(FilamentUi::text('Ready For Sourcing'))
                            ->inline(false)
                            ->disabled(),
                        Textarea::make('justification')
                            ->label(FilamentUi::field('justification'))
                            ->columnSpanFull(),
                        Textarea::make('rejection_reason')
                            ->label(FilamentUi::field('rejection_reason'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                    ]),
            ])->columnSpanFull(),
        ]);
    }
}
