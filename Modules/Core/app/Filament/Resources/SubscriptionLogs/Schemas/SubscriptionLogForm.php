<?php

namespace Modules\Core\Filament\Resources\SubscriptionLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class SubscriptionLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        TextInput::make('action')
                            ->label(FilamentUi::field('action'))
                            ->required(),
                        Select::make('previous_plan_id')
                            ->label(FilamentUi::field('previous_plan_id'))
                            ->relationship('previousPlan', 'name'),
                        Select::make('new_plan_id')
                            ->label(FilamentUi::field('new_plan_id'))
                            ->relationship('newPlan', 'name'),
                    ]),

                Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(FilamentUi::field('amount'))
                            ->numeric(),
                        TextInput::make('currency')
                            ->label(FilamentUi::field('currency')),
                        TextInput::make('payment_method')
                            ->label(FilamentUi::field('payment_method')),
                        TextInput::make('payment_status')
                            ->label(FilamentUi::field('payment_status')),
                        TextInput::make('payment_proof')
                            ->label(FilamentUi::field('payment_proof')),
                        TextInput::make('invoice_number')
                            ->label(FilamentUi::field('invoice_number')),
                        TextInput::make('invoice_url')
                            ->label(FilamentUi::field('invoice_url'))
                            ->url(),
                    ]),

                Section::make('Period')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('period_start'),
                        DateTimePicker::make('period_end'),
                    ]),

                Section::make('Additional')
                    ->columns(2)
                    ->schema([
                        Textarea::make('notes')
                            ->label(FilamentUi::field('notes'))
                            ->columnSpanFull(),
                        TextInput::make('processed_by')
                            ->label(FilamentUi::field('processed_by'))
                            ->numeric(),
                        Textarea::make('metadata')
                            ->label(FilamentUi::field('metadata'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
